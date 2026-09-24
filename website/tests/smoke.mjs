// Smoke test of the demo website in a headless Chrome, through the DevTools protocol.
//
// It serves public/ with PHP's built-in server, loads the page, waits for the wasm PHP,
// submits the form and checks the result. No dependency: Node 22+ (built-in fetch and
// WebSocket) and Chrome or Chromium in the PATH (or the CHROME environment variable).
//
// Run `castor website:wasm:export --pack` first (with `--build` the first time), then
// `castor website:smoke` or `node website/tests/smoke.mjs`.

import { spawn, spawnSync } from 'node:child_process';
import { existsSync, mkdtempSync, readFileSync, rmSync } from 'node:fs';
import { createServer } from 'node:net';
import { tmpdir } from 'node:os';
import { dirname, join, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const WEB_ROOT = resolve(dirname(fileURLToPath(import.meta.url)), '../public');
const CHROME_CANDIDATES = ['google-chrome', 'google-chrome-stable', 'chromium', 'chromium-browser'];

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

const freePort = () => new Promise((resolve, reject) => {
    const server = createServer();
    server.unref();
    server.on('error', reject);
    server.listen(0, '127.0.0.1', () => {
        const { port } = server.address();
        server.close(() => resolve(port));
    });
});

const findChrome = () => {
    const candidates = process.env.CHROME ? [process.env.CHROME] : CHROME_CANDIDATES;
    const found = candidates.find((bin) => spawnSync(bin, ['--version'], { stdio: 'ignore' }).status === 0);
    if (!found) {
        throw new Error(`No Chrome found (tried ${candidates.join(', ')}); set the CHROME environment variable`);
    }

    return found;
};

// Decodes the HTML entities of the raw result: the wasm PHP's libxml serializes non-ASCII characters as entities
const DECODE = `(raw => { const t = document.createElement('textarea'); t.innerHTML = raw; return t.value; })`;

let failures = 0;
const check = (label, ok, detail = '') => {
    console.log(`${ok ? 'PASS' : 'FAIL'} ${label}${detail ? `: ${detail}` : ''}`);
    if (!ok) failures++;
};

if (!existsSync(join(WEB_ROOT, 'build/php-web.mjs'))) {
    console.error(`${WEB_ROOT}/build is missing: run "castor website:wasm:export --pack" first (add --build the first time)`);
    process.exit(2);
}

const chromeBin = findChrome();
const httpPort = await freePort();
const page = `http://127.0.0.1:${httpPort}/`;
const userDataDir = mkdtempSync(join(tmpdir(), 'jolitypo-smoke-'));

const php = spawn('php', ['-S', `127.0.0.1:${httpPort}`, '-t', WEB_ROOT], { stdio: 'ignore' });
const chrome = spawn(chromeBin, [
    '--headless=new', '--remote-debugging-port=0', '--no-first-run', '--disable-gpu', `--user-data-dir=${userDataDir}`, 'about:blank',
], { stdio: 'ignore' });

const cleanup = () => {
    chrome.kill();
    php.kill();
    rmSync(userDataDir, { recursive: true, force: true });
};
process.on('exit', cleanup);

try {
    // Wait for the PHP server and for Chrome's DevTools endpoint (Chrome writes the port it picked in the profile)
    for (let i = 0; i < 50; i++) {
        // The body must be consumed, or Node's HTTP parser crashes when the server closes the connection
        try { await (await fetch(page, { method: 'HEAD' })).arrayBuffer(); break; } catch { await sleep(200); }
    }
    let cdpPort;
    for (let i = 0; i < 100 && !cdpPort; i++) {
        try { cdpPort = Number(readFileSync(join(userDataDir, 'DevToolsActivePort'), 'utf8').split('\n')[0]); } catch { await sleep(200); }
    }
    if (!cdpPort) throw new Error('Chrome DevTools endpoint did not come up');

    const target = await (await fetch(`http://127.0.0.1:${cdpPort}/json/new?${page}`, { method: 'PUT' })).json();
    const ws = new WebSocket(target.webSocketDebuggerUrl);
    await new Promise((resolve, reject) => { ws.onopen = resolve; ws.onerror = reject; });

    let nextId = 1;
    const pending = new Map();
    ws.onmessage = (event) => {
        const message = JSON.parse(event.data);
        if (message.id && pending.has(message.id)) {
            pending.get(message.id)(message);
            pending.delete(message.id);
        }
    };
    const send = (method, params) => new Promise((resolve) => {
        const id = nextId++;
        pending.set(id, resolve);
        ws.send(JSON.stringify({ id, method, params }));
    });
    const evaluate = async (expression, awaitPromise = false) => {
        const { result } = await send('Runtime.evaluate', { expression, awaitPromise, returnByValue: true });
        if (result?.exceptionDetails) {
            throw new Error(result.exceptionDetails.exception?.description ?? JSON.stringify(result.exceptionDetails));
        }

        return result?.result?.value;
    };

    // Wait for the document, then for wasm.js: importing the page's own module resolves once its top-level await
    // (the PHP module instantiation) and main() ran, so the form listeners are attached
    for (let i = 0; i < 100; i++) {
        if (await evaluate(`document.readyState === 'complete' && location.href.startsWith(${JSON.stringify(page)})`)) break;
        await sleep(200);
    }
    const started = Date.now();
    await evaluate(`import(${JSON.stringify(`${page}js/wasm.js`)}).then(() => true)`, true);
    check('wasm PHP ready', true, `${Math.round((Date.now() - started) / 1000)}s`);

    // A second module instance, with its own copy of the packed /app, to look at the PHP itself
    const phpInfo = await evaluate(`import(${JSON.stringify(`${page}build/php-web.mjs`)}).then(async (module) => {
        const buffer = [];
        const { ccall, FS } = await module.default({ print(data) { buffer.push(data); } });
        FS.unlink('/app/src/index.php');
        FS.writeFile('/app/src/index.php', '<?php require "/app/vendor/autoload.php"; echo json_encode(["php" => PHP_VERSION, "intl" => extension_loaded("intl"), "normalizer" => class_exists("Normalizer"), "jolitypo" => class_exists("JoliTypo\\\\Fixer")]);');
        ccall('phpw', null, ['string'], ['/app/src/index.php']);
        return JSON.parse(buffer.join(''));
    })`, true);
    check('JoliTypo and a Normalizer (intl or polyfill) are packed', phpInfo.jolitypo && phpInfo.normalizer, `PHP ${phpInfo.php}, intl: ${phpInfo.intl}`);

    const submit = async ({ content, locale, uncheck = [] }) => {
        await evaluate(`(() => {
            const content = document.getElementById('typo_fixer_content');
            ${content === undefined ? '' : `content.value = ${JSON.stringify(content)};`}
            content.dispatchEvent(new Event('change', { bubbles: true }));
            const localeElt = document.getElementById('typo_fixer_locale');
            ${locale === undefined ? '' : `localeElt.value = ${JSON.stringify(locale)};`}
            localeElt.dispatchEvent(new Event('change', { bubbles: true }));
            for (const input of document.querySelectorAll('input[name="typo_fixer[fixers][]"]')) {
                input.checked = !${JSON.stringify(uncheck)}.includes(input.value);
            }
            document.getElementById('resultContent').textContent = '';
            document.querySelector('form[name="typo_fixer"]').requestSubmit();
        })()`);
        for (let i = 0; i < 50; i++) {
            const state = await evaluate(`(() => {
                const raw = document.getElementById('resultContent').textContent;
                return {
                    url: location.href,
                    raw,
                    result: ${DECODE}(raw),
                    code: document.getElementById('phpCode').innerText,
                    visible: !document.getElementById('result').classList.contains('u-d(none)'),
                    checked: Array.from(document.querySelectorAll('input[name="typo_fixer[fixers][]"]')).filter(i => i.checked).map(i => i.value),
                };
            })()`);
            if (state.raw.trim()) return state;
            await sleep(200);
        }
        throw new Error('no result after submit');
    };
    const fixersInCode = (code) => JSON.parse(code.match(/new Fixer\((\[.*?\])\)/)?.[1] ?? 'null');

    // The sample content of the page, as a visitor would submit it
    const sample = await submit({});
    check('submit stays on the page', sample.url === page, sample.url);
    check('result panel shown', sample.visible);
    check('no PHP error in the result', !/(Fatal error|Warning|Deprecated|Exception)/.test(sample.result), JSON.stringify(sample.result.slice(0, 200)));
    check('PHP code lists the checked fixers, in page order', JSON.stringify(fixersInCode(sample.code)) === JSON.stringify(sample.checked), sample.code.split('\n').find((line) => line.includes('new Fixer')));
    check('PHP code carries the locale', sample.code.includes(`setLocale(${JSON.stringify(await evaluate(`document.getElementById('typo_fixer_locale').value`))})`));

    // A few stable effects, on "café" and "élève" written with combining accents (NFD)
    const content = '<p>Le café de l\'élève est prêt ! Vraiment...</p>';
    const fr = await submit({ content, locale: 'fr' });
    console.log(`  raw fr result: ${fr.raw}`);
    check('fr: decomposed accents composed (UnicodeNormalization)', /café/.test(fr.result) && /élève/.test(fr.result) && !/[̀-ͯ]/.test(fr.result), JSON.stringify(fr.result));
    check('fr: ellipsis', /…/.test(fr.result));
    check('fr: narrow no-break space before ! (SpaceBeforePunctuation)', / !/.test(fr.result));
    check('fr: curly apostrophe (CurlyQuote)', /l’élève/.test(fr.result));

    const en = await submit({ content, locale: 'en' });
    check('en: space before ! removed (SpaceBeforePunctuation)', /prêt!/.test(en.result), JSON.stringify(en.result));

    const without = await submit({ content, locale: 'fr', uncheck: ['UnicodeNormalization'] });
    check('unchecked fixer left out of the PHP code', !fixersInCode(without.code).includes('UnicodeNormalization') && fixersInCode(without.code).length === fr.checked.length - 1);
    check('unchecked fixer not applied (combining accents kept)', /é/.test(without.result), JSON.stringify(without.raw));

    ws.close();
} catch (error) {
    console.error('ERROR', error);
    failures++;
} finally {
    cleanup();
}

console.log(failures ? `${failures} check(s) failed` : 'All checks passed');
process.exit(failures ? 1 : 0);

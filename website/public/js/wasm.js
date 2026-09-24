import phpBinary from "../build/php-web.mjs";
import { highlightHtml, highlightPhp } from "./highlight.js";

const buffer = [];
const {ccall, FS} = await phpBinary({
    print(data) {
        buffer.push(data);
    }
})

const formElt = document.querySelector('form[name="typo_fixer"]');
const contentElt = document.getElementById('typo_fixer_content');
const localeElt = document.getElementById('typo_fixer_locale');
// The fixers run in the order of the page, so UnicodeNormalization comes first
const fixerElts = document.querySelectorAll('input[name="typo_fixer[fixers][]"]');
const submitElt = document.querySelector('.js-submit');
const statusElt = document.querySelector('.js-status');
const previewElt = document.getElementById('preview');
const resultContentElt = document.getElementById('resultContent');
const phpCodeElt = document.getElementById('phpCode');

const getFixers = () => Array.from(fixerElts).filter(elt => elt.checked).map(elt => elt.value);

const phpString = (value) => `'${value.replace(/[\\']/g, '\\$&')}'`;

// The content goes in a nowdoc, so nothing in it is interpreted: pick a closing label it does not contain
const nowdocLabel = (content) => {
    let label = 'HTML';
    for (let i = 2; new RegExp(`^[ \\t]*${label}\\b`, 'm').test(content); i++) {
        label = `HTML${i}`;
    }

    return label;
};

const buildPhpFixerCode = (fixers, locale, content, autoload) => {
    const label = nowdocLabel(content);
    const rules = `[\n${fixers.map(fixer => `    ${phpString(fixer)},\n`).join('')}]`;

    return `<?php

require ${phpString(autoload)};

use JoliTypo\\Fixer;

$fixer = new Fixer(${rules});
$fixer->setLocale(${phpString(locale)});

$html = <<<'${label}'
${content}
${label};

echo $fixer->fix($html);
`;
};

const runPhpCode = (phpCode) => {
    FS.unlink('/app/src/index.php')
    FS.writeFile('/app/src/index.php', phpCode);

    ccall("phpw", null, ["string"], ["/app/src/index.php"]);
    // Emscripten calls print() once per line, without the line feed
    const output = buffer.join('\n');
    buffer.length = 0;

    return output;
}

const previewDocument = (html, locale) => `<!DOCTYPE html>
<html lang="${locale.replace(/_/g, '-').replace(/[^\w-]/g, '')}">
<head>
<meta charset="UTF-8">
<style>
    :root { color-scheme: light dark; }
    body { margin: 0; padding: 20px 24px; max-width: 68ch; font: 18px/1.65 "Iowan Old Style", "Palatino Linotype", Palatino, Georgia, serif; color: #2b2622; background: #fff; }
    a { color: #9a5600; }
    @media (prefers-color-scheme: dark) { body { color: #eee7dd; background: #1d1a16; } a { color: #fdbb66; } }
</style>
</head>
<body>${html}</body>
</html>`;

const update = () => {
    const fixers = getFixers();
    const locale = localeElt.value || 'en';
    const content = contentElt.value;

    // JoliTypo refuses an empty rule set: without any fixer, show the content as is
    if (!fixers.length) {
        previewElt.srcdoc = previewDocument(content, locale);
        resultContentElt.innerHTML = highlightHtml(content);
        phpCodeElt.innerHTML = highlightPhp('<?php\n\n// Select at least one fixer: JoliTypo needs at least one rule.\n');
        statusElt.classList.remove('output__status--error');
        statusElt.textContent = 'No fixer selected, the content is unchanged';

        return;
    }

    const started = performance.now();
    const result = runPhpCode(buildPhpFixerCode(fixers, locale, content, '/app/vendor/autoload.php'));
    const elapsed = Math.round(performance.now() - started);

    previewElt.srcdoc = previewDocument(result, locale);
    resultContentElt.innerHTML = highlightHtml(result);
    phpCodeElt.innerHTML = highlightPhp(buildPhpFixerCode(fixers, locale, content, 'vendor/autoload.php'));

    const failed = /(Fatal error|Uncaught|Warning|Deprecated):/.test(result);
    statusElt.classList.toggle('output__status--error', failed);
    statusElt.textContent = failed
        ? 'PHP reported an error, see the HTML tab'
        : `${fixers.length} fixer${fixers.length > 1 ? 's' : ''}, ${elapsed} ms`;
};

let timer;
const scheduleUpdate = () => {
    clearTimeout(timer);
    timer = setTimeout(update, 250);
};

formElt.addEventListener('input', scheduleUpdate);
formElt.addEventListener('change', scheduleUpdate);
formElt.addEventListener('submit', (e) => {
    e.preventDefault();
    clearTimeout(timer);
    update();
});
contentElt.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) {
        e.preventDefault();
        formElt.requestSubmit();
    }
});

submitElt.disabled = false;
submitElt.querySelector('.js-submit-label').textContent = 'Fix my typography';
update();

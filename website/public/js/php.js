// JoliTypo in the wasm PHP, without the DOM: wasm.js wires it to the page, tests/page.test.mjs tests it in node.
import phpBinary from "../build/php-web.mjs";

const buffer = [];
const {ccall, FS} = await phpBinary({
    print(data) {
        buffer.push(data);
    }
})

const phpString = (value) => `'${value.replace(/[\\']/g, '\\$&')}'`;

// The content goes in a nowdoc, so nothing in it is interpreted: pick a closing label it does not contain
const nowdocLabel = (content) => {
    let label = 'HTML';
    for (let i = 2; new RegExp(`^[ \\t]*${label}\\b`, 'm').test(content); i++) {
        label = `HTML${i}`;
    }

    return label;
};

export const buildPhpFixerCode = (fixers, locale, content, autoload) => {
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

// What PHP prints: the fixed content, or an error
export const fix = (fixers, locale, content) => runPhpCode(buildPhpFixerCode(fixers, locale, content, '/app/vendor/autoload.php'));

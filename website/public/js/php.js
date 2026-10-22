// JoliTypo in the wasm PHP, without the DOM: wasm.js wires it to the page, tests/page.test.mjs tests it in node.
import createPhp from "../build/php-web.mjs";

const php = await createPhp();

// What PHP prints: the fixed content, or an error
export const fix = async (fixers, locale, content) => {
    const { stdout } = await php.runFile('/app/src/fix.php', { args: [locale, ...fixers], stdin: content });

    return stdout;
};

const phpString = (value) => `'${value.replace(/[\\']/g, '\\$&')}'`;

// The content goes in a nowdoc, so nothing in it is interpreted: pick a closing label it does not contain
const nowdocLabel = (content) => {
    let label = 'HTML';
    for (let i = 2; new RegExp(`^[ \\t]*${label}\\b`, 'm').test(content); i++) {
        label = `HTML${i}`;
    }

    return label;
};

// The PHP code shown to the visitor, that does what fix() does: it never runs. Keep in sync with src/fix.php
export const buildPhpFixerCode = (fixers, locale, content) => {
    const label = nowdocLabel(content);
    const rules = `[\n${fixers.map(fixer => `    ${phpString(fixer)},\n`).join('')}]`;

    return `<?php

require 'vendor/autoload.php';

use JoliTypo\\Fixer;

$fixer = new Fixer(${rules});
$fixer->setLocale(${phpString(locale)});

$html = <<<'${label}'
${content}
${label};

echo $fixer->fix($html);
`;
};

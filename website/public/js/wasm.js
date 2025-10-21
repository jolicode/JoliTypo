import phpBinary from "../build/php-web.mjs";

const buffer = [];
const {ccall, FS} = await phpBinary({
    print(data) {
        buffer.push(data);
    }
})

const main = () => {
    const contentElt = document.getElementById('typo_fixer_content');
    let content = contentElt.value || 'test';
    contentElt.addEventListener('change', (e) => {
        content = e.target.value;
    });

    const localeElt = document.getElementById('typo_fixer_locale');
    let locale = localeElt.value || 'en';
    localeElt.addEventListener('change', (e) => {
        locale = e.target.value;
    });

    let fixers = ['Dash', 'Dimension', 'Ellipsis', 'SmartQuotes', 'NoSpaceBeforeComma', 'Hyphen', 'CurlyQuote', 'Trademark', 'Unit'];
    const fixerElts = document.querySelectorAll('input[name="typo_fixer[fixers][]"]');
    for (const fixerElt of fixerElts) {
        fixerElt.addEventListener('change', (e) => {
            if (e.target.checked) {
                fixers.push(e.target.value);
            } else {
                fixers = fixers.filter(f => f !== e.target.value);
            }
        });
    }

    const resultElt = document.getElementById('result');
    const phpCodeElt = document.getElementById('phpCode');
    const resultContentElt = document.getElementById('resultContent');

    const formElt = document.querySelector('form[name="typo_fixer"]');
    formElt.addEventListener('change', (e) => {
        resultElt.classList.add('u-d(none)');
    });
    formElt.addEventListener('submit', (e) => {
        e.preventDefault();
        update(fixers, locale, content, resultElt, phpCodeElt, resultContentElt);
    });
}

main();

const update = async (fixers, locale, content, resultElt, phpCodeElt, resultContentElt) => {
    const phpCode = buildPhpFixerCode(fixers, locale, content);
    const phpHighlighCode = buildPhpHighlighCode(phpCode);
    phpCodeElt.innerHTML = await runPhpCode(phpHighlighCode);
    resultContentElt.textContent = await runPhpCode(phpCode);
    resultElt.classList.remove('u-d(none)');
}

const buildPhpFixerCode = (fixers, locale, content) => {
    return `<?php

require_once '/app/vendor/autoload.php';

use JoliTypo\\Fixer;

$fixer = new Fixer(${JSON.stringify(fixers)});
$fixer->setLocale(${JSON.stringify(locale)});

echo $fixer->fix(${JSON.stringify(content)});`;
};

const buildPhpHighlighCode = (phpCode) => {
    return `<?php
echo nl2br(highlight_string(<<<'PHP'
${phpCode}
PHP, true));`;
};

const runPhpCode = async (phpCode) => {
    FS.unlink('/app/src/index.php')
    FS.writeFile('/app/src/index.php', phpCode);

    ccall("phpw", null, ["string"], ["/app/src/index.php"]);
    const output = buffer.join('');
    buffer.length = 0;

    return output;
}

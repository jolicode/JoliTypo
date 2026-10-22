// Tests of the module of the page that runs JoliTypo, in node: `castor website:wasm:check` runs them with
// fetch-files.mjs of lyrixx/php-wasm-builder preloaded, so that the module loads public/build/php-web.mjs as the page
// does.

import assert from 'node:assert/strict';
import { test } from 'node:test';

import { buildPhpFixerCode, fix } from '../public/js/php.js';

test('JoliTypo fixes a text', async () => {
    // UnicodeNormalization runs on the polyfill, SpaceBeforePunctuation follows the locale
    const fixed = await fix(['UnicodeNormalization', 'Ellipsis', 'SpaceBeforePunctuation', 'CurlyQuote'], 'fr', "<p>Le café de l'élève est prêt ! Vraiment...</p>");

    assert.equal(fixed, '<p>Le café de l’élève est prêt ! Vraiment…</p>');
});

test('the content reaches PHP as it is', async () => {
    // Nothing for Ellipsis to fix, and nothing for PHP to interpret: it is on stdin
    const content = "<p>It's a \\ backslash, $var {$x} 'quoted'</p>\nHTML;\n<p>HTML</p>";

    assert.equal(await fix(['Ellipsis'], 'en', content), content);
});

test('the PHP code shown to the visitor requires the autoload of its project', () => {
    assert.match(buildPhpFixerCode(['Ellipsis'], 'fr', '<p>...</p>'), /^<\?php\n\nrequire 'vendor\/autoload\.php';\n/);
});

test('the PHP code shown to the visitor keeps the content in a nowdoc it does not close', () => {
    assert.match(buildPhpFixerCode(['Ellipsis'], 'en', '<p>HTML</p>\nHTML;'), /<<<'HTML2'\n<p>HTML<\/p>\nHTML;\nHTML2;\n/);
});

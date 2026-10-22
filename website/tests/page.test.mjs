// Tests of the module of the page that runs JoliTypo, in node: `castor website:wasm:check` runs them with page.mjs of
// lyrixx/php-wasm-builder preloaded, so that the module loads public/build/php-web.mjs as the page does.

import assert from 'node:assert/strict';
import { test } from 'node:test';

import { buildPhpFixerCode, fix } from '../public/js/php.js';

test('JoliTypo fixes a text', () => {
    // UnicodeNormalization runs on the polyfill, SpaceBeforePunctuation follows the locale
    const fixed = fix(['UnicodeNormalization', 'Ellipsis', 'SpaceBeforePunctuation', 'CurlyQuote'], 'fr', "<p>Le café de l'élève est prêt ! Vraiment...</p>");

    assert.equal(fixed, '<p>Le café de l’élève est prêt ! Vraiment…</p>');
});

test('the content reaches PHP as it is', () => {
    // Nothing for Ellipsis to fix, nothing for PHP to interpret, and a line that would close the nowdoc
    const content = "<p>It's a \\ backslash, $var {$x} 'quoted'</p>\nHTML;\n<p>HTML</p>";

    assert.equal(fix(['Ellipsis'], 'en', content), content);
});

test('the PHP code shown to the visitor requires the autoload of its project', () => {
    assert.match(buildPhpFixerCode(['Ellipsis'], 'fr', '<p>...</p>', 'vendor/autoload.php'), /^<\?php\n\nrequire 'vendor\/autoload\.php';\n/);
});

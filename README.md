<h1 align="center">
  <a href="https://github.com/jolicode/JoliTypo"><img src="https://jolicode.com/media/original/oss/headers/jolitypo.png?v3" alt="JoliTypo"></a>
  <br />
  JoliTypo – Web Microtypography fixer<br>
  <!-- 
  this allow subtitles without the h1 border between them.
  sub and em for small text and italic
  h6 for smaller and gray color
  -->
  <sub><em><h6>Finally a tool for typography nerds.</h6></em></sub>
</h1>

<div align="center">

[![PHP Version Require](http://poser.pugx.org/jolicode/jolitypo/require/php)](https://packagist.org/packages/jolicode/jolitypo)
[![Monthly Downloads](http://poser.pugx.org/jolicode/jolitypo/d/monthly)](https://packagist.org/packages/jolicode/jolitypo)

</div>

# Introduction

JoliTypo is a tool fixing [Microtypography](https://en.wikipedia.org/wiki/Microtypography) glitches inside your HTML content. When your CMS outputs `" "` instead of `“ ”` or `...` instead of `…`, JoliTypo can help.

```php
use JoliTypo\Fixer;

// Create a Fixer, specify the fixes
$fixer = new Fixer(['Ellipsis', 'Dash', 'SmartQuotes', 'CurlyQuote', 'Hyphen']);

// Get HTML content with correct microtypography
$fixedContent = $fixer->fix(
    '<p>"Tell me Mr. Anderson... what good is a phone call... if you\'re unable to speak?" -- Agent Smith, <em>Matrix</em>.</p>'
);
```
```html
<p>“Tell me Mr. Ander­son… what good is a phone call… if you’re unable to speak?”—Agent Smith, <em>Matrix</em>.</p>
```
> “Tell me Mr. Anderson… what good is a phone call… if you’re unable to speak?”—Agent Smith, Matrix.

The output is plain UTF-8, not HTML entities. The soft hyphen inserted by the `Hyphen` fixer inside "Anderson" is an invisible character.

It's designed to be:

- language agnostic (you can fix `fr_FR`, `fr_CA`, `en_US`... you tell JoliTypo what to fix);
- easy to integrate into modern PHP projects (composer and autoload);
- robust (makes use of the HTML5 parser of PHP, `\Dom\HTMLDocument`, instead of parsing HTML with dummy regexp);
- smart enough to avoid JavaScript, Code, CSS processing... (configurable protected tags list);
- fully tested;
- fully open and usable in any project (MIT License).

You can try it with the [online demo](https://jolitypo.jolicode.com/)!

Quick usage
===========

Just tell the Fixer class [which Fixer](#available-fixers) you want to run on your content and then, call `fix()`:

```php
use JoliTypo\Fixer;

$fixer = new Fixer(['SmartQuotes', 'SpaceBeforePunctuation']);
$fixer->setLocale('fr_FR');

$fixedContent = $fixer->fix('<p>Je suis "très content" de t\'avoir invité sur <a href="http://jolicode.com/">Jolicode.com</a> !</p>');
```

For your ease of use, you can find [ready to use list of Fixer for your language here](#fixer-recommendations-by-locale).
Micro-typography is nothing like a standard or a law, what really matters is consistency, so feel free to use your own lists.

> [!NOTE]
> Please be advised that JoliTypo works best on **HTML content**; it will also work on plain text, but will be less smart about smart quotes. Both HTML fragments and complete HTML documents (with a doctype, `<html>`, `<head>` and `<body>`) are accepted, and a complete document is returned as a complete document. The output is always UTF-8, never HTML entities: make sure your page declares this charset.

To fix non HTML content, use the `fixString()` method:

```php
use JoliTypo\Fixer;

$fixer = new Fixer(["Trademark", "SmartQuotes"]);
$fixedContent = $fixer->fixString('Here is a "protip(c)"!'); // Here is a “protip©”!
```

CLI usage
=========

You can run a standalone version of JoliTypo by downloading [the PHAR version](https://github.com/jolicode/JoliTypo/releases/latest)

Run `jolitypo --help` to know how to configure the Fixer.

Installation
============

Requirements are handled by Composer (PHP 8.4 or higher, with the dom, libxml and mbstring extensions; intl is optional but recommended).

```
composer require jolicode/jolitypo
```

*Usage outside composer is also possible, just add the `src/` directory to any PSR-0 compatible autoloader.*

Integrations
===========

- (Built-in) [Symfony Bundle](src/JoliTypo/Bridge/Symfony)
- (Built-in) [Twig extension](src/JoliTypo/Bridge/Twig)
- (Built-in) [CLI](https://github.com/jolicode/JoliTypo/releases/latest)
- [Drupal module](https://www.drupal.org/project/typography_filter)
- [Joomla plugin](https://github.com/YGomiero/typographe)
- [MODX Extra](https://github.com/jenswittmann/JoliTypo)

🚨 There are no WordPress plugin anymore, feel free to build one!

Available Fixers
================

UnicodeNormalization
--------------------

Converts the text to Unicode Normalization Form C (NFC, canonical composition). Content pasted from PDF files, macOS or
some editors can contain decomposed characters, a base letter followed by combining marks: `e` + U+0301 instead of `é`.
Both render the same but are different strings, which breaks search, comparisons and hyphenation.

Only the lossless canonical form is applied. The compatibility form (NFKC) is deliberately not offered, as it would turn
the no-break spaces, ellipsis and trademark sign produced by the other fixers back into plain ASCII.

Put this fixer first in your list, so that the other fixers work on composed characters: `Hyphen`, for instance, counts
a combining mark as a letter and hyphenates decomposed words differently.

This fixer relies on the `Normalizer` class of the `intl` extension, with `symfony/polyfill-intl-normalizer` as a
fallback when the extension is not installed.

Dash
----

Replaces the simple dash `-` by a ndash `–` between numbers (dates ranges...) and the double `--` by a mdash `—`.

It also binds the spaces around a dash, so that it never ends up alone at the beginning or at the end of a line.
A narrow no-break space (`U+202F`, which [is not rendered everywhere](#compatibility--os-support-restrictions)) replaces the space on the
side the dash belongs to, and the other side is left as it was written. A space is never inserted where there was
none, so `1964–2009` stays untouched. Three cases are told apart, `[nnbsp]` standing for that space below:

| | Input | Output |
|---|---|---|
| a pair of dashes marks an incise, the first binds forward and the second backward | `Style - not sincerity - is…` | `Style –[nnbsp]not sincerity[nnbsp]– is…` |
| a range opens and closes nothing, so it holds together on both sides | `1964 - 2009` | `1964[nnbsp]–[nnbsp]2009` |
| a dash on its own could be anything, so it binds to what precedes it | `text – more text` | `text[nnbsp]– more text` |

A pair spans neither two sentences nor a line break. Three dashes or more in the same sentence could be a list or a
route rather than an incise, so they all keep the spacing of a lone dash.

Dimension
---------

Replaces the letter x between numbers (`12 x 123`) by a times entity (`×`, the real mathematical symbol).

Ellipsis
--------

Replaces the three dots `...` by an ellipsis `…`.

SmartQuotes
-----------

Converts dumb quotes `" "` to all kinds of smart style quotation marks (`“ ”`, `« »`, `„ “`...). Handles a good variety of locales,
like English, Arabic, French, Italian, Spanish, Irish, German...

Pairs of straight single quotes `' '` are converted to the nested (second-level) quotation marks of the locale:
`‘ ’` in English, `‚ ‘` in German, `“ ”` in French, Spanish or Italian, `‹ ›` in Swiss German...
Apostrophes (`I'm`, `l'univers`) are left untouched, `CurlyQuote` takes care of them.

This Fixer must be placed **before** `CurlyQuote` in your rules, otherwise the closing single quote is mistaken for an apostrophe:

```php
$fixer = new Fixer(['SmartQuotes', 'CurlyQuote']);
$fixer->setLocale('en_GB');
echo $fixer->fix('<p>"This \'magic\' piece of code fixes quotes and apostrophes, doesn\'t it?"</p>');
// <p>“This ‘magic’ piece of code fixes quotes and apostrophes, doesn’t it?”</p>
```

Custom quotation marks can be set with `setOpening()`, `setClosing()`, `setNestedOpening()` and `setNestedClosing()`.
For instance, German books and newspapers often use reversed guillemets (`»…«` and `›…‹`) instead of `„…“` and `‚…‘`:

```php
use JoliTypo\Fixer;

$smartQuotes = new Fixer\SmartQuotes('de_DE');
$smartQuotes->setOpening('»');
$smartQuotes->setClosing('«');
$smartQuotes->setNestedOpening('›');
$smartQuotes->setNestedClosing('‹');

$fixer = new Fixer(['Ellipsis', 'Dash', $smartQuotes, 'CurlyQuote']);
echo $fixer->fix('<p>And this is an "example with another \'single quote\' inside".</p>');
// <p>And this is an »example with another ›single quote‹ inside«.</p>
```

Note that calling `setLocale()` on the `Fixer` resets the quotation marks to the defaults of the locale.

See `LocaleConfig::QUOTE_STYLES_BY_LOCALE` and `LocaleConfig::NESTED_QUOTE_STYLES_BY_LOCALE` for the default quotation marks of each language,
and [the code](https://github.com/jolicode/JoliTypo/blob/master/src/JoliTypo/Fixer/SmartQuotes.php) for more details.
Do not forget to specify a locale on the Fixer instance.

This Fixer replaces legacy `EnglishQuotes`, `FrenchQuotes` and `GermanQuotes`.

SpaceBeforePunctuation
----------------------

Locale-aware fixer for spacing before punctuation marks. Handles:
- **French** (`fr`, `fr_FR`, `fr_BE`, `fr_CH`): Adds non-breaking space before `:` and thin non-breaking space before `;`, `!`, `?`
- **Canadian French** (`fr_CA`): No space before punctuation (follows English conventions)
- **Swiss German** (`de_CH`): Uses French-style guillemets with thin spaces
- **All other locales**: Removes any incorrect space before punctuation

This fixer requires a locale to be set on the Fixer with `$fixer->setLocale('fr_FR');`.

FrenchNoBreakSpace (deprecated)
-------------------------------

> [!WARNING]
> This fixer is deprecated. Use `SpaceBeforePunctuation` instead.

Replaces some classic spaces by non-breaking spaces following the French typographic code.
No break space are placed before `:`, thin no break space before `;`, `!` and `?`.

NoSpaceBeforeComma
------------------

Removes space before `,` and makes sure there is only one space after.

Hyphen (automatic hyphenation)
------------------------------

Makes use of `org_heigl/hyphenator`, a tool enabling word-hyphenation in PHP.
This Hyphenator uses the pattern-files from OpenOffice which are based on the pattern-files created for TeX.

There are only some locales available for this fixer: af_ZA, ca, da_DK, de_AT, de_CH, de_DE, en_GB, en_UK, et_EE, fr, hr_HR, hu_HU, it_IT, lt_LT, nb_NO, nn_NO, nl_NL, pl_PL, pt_BR, ro_RO, ru_RU, sk_SK, sl_SI, sr, zu_ZA.

You can read more about this fixer on [the official github repository](https://github.com/heiglandreas/Org_Heigl_Hyphenator).

**This Fixer requires a Locale to be set on the Fixer with `$fixer->setLocale('fr_FR');`. Default to `en_GB`.**

The hyphenation can be tuned by giving an instance of the fixer instead of its name:

```php
use JoliTypo\Fixer;
use JoliTypo\Fixer\Hyphen;

$fixer = new Fixer(['Ellipsis', new Hyphen('fr_FR', leftMin: 3, rightMin: 3, wordMin: 8)]);
```

| Option     | Default | Description                                                                    |
|------------|---------|--------------------------------------------------------------------------------|
| `leftMin`  | `4`     | Minimum number of characters kept before the first hyphenation point of a word |
| `rightMin` | `3`     | Minimum number of characters kept after the last hyphenation point of a word   |
| `wordMin`  | `6`     | Minimum length of a word (in characters) to be hyphenated                      |

These options are kept when the locale is changed with `$fixer->setLocale()`.

Words already containing a soft hyphen (`&shy;`) are left untouched: already fixed content can safely be fixed again, and manual hyphenation points are preserved.

**Proper hyphenation is mandatory in justified text** and you should avoid word breaking in titles with this line of CSS: `hyphens:none;`.

⚠ Be aware that the current screen readers are unable to spell correctly the words containing `&shy;` tags. The Hyphen filter should therefore be used with caution or you might reduce your website's accessibility.

CurlyQuote (Smart Quote)
-----------------------

Replaces straight quotes `'` with curly ones `’`.
There is one exception to consider: foot and inch marks (minutes and second marks). Purists use prime `′`, this fixer uses straight quotes for compatibility.
[Read more about Curly quotes](http://practicaltypography.com/straight-and-curly-quotes.html).

Pairs of single quotes (`'quoted'`) are converted to nested quotation marks by `SmartQuotes`, which must run before this Fixer.

Trademark
---------

Handles trade­mark symbol `™`, a registered trade­mark symbol `®`, and a copy­right symbol `©`. This fixer replaces
commonly used approximations: `(r)`, `(c)` and `(TM)`. A non-breaking space is put between numbers and copyright symbols too.

Unit (formerly Numeric)
---------

Adds a non-breaking space between a numeral and its unit. Like this: `12_h`, `42_฿` or `88_%`. It was named `Numeric` before release 1.0.2, but BC is kept for now.

**It is really easy to make your own Fixers, feel free to extend the provided ones if they do not fit your typographic rules.**

Fixer recommendations by locale
===============================

en_GB
-----

```php
$fixer = new Fixer(['UnicodeNormalization', 'Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark']);
$fixer->setLocale('en_GB');
```

fr_FR
-----

Those rules apply for most of the recommendations of "Abrégé du code typographique à l'usage de la presse", ISBN: 9782351130667.

```php
$fixer = new Fixer(['UnicodeNormalization', 'Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'SpaceBeforePunctuation', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark']);
$fixer->setLocale('fr_FR');
```

fr_CA
-----

Mostly the same as fr_FR, but the space before punctuation points is not mandatory.

```php
$fixer = new Fixer(['UnicodeNormalization', 'Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark']);
$fixer->setLocale('fr_CA');
```

de_DE
-----

Mostly the same as en_GB, according to [Typefacts](http://typefacts.com/) and [Wikipedia](http://de.wikipedia.org/wiki/Typografie_f%C3%BCr_digitale_Texte).

```php
$fixer = new Fixer(['UnicodeNormalization', 'Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark']  );
$fixer->setLocale('de_DE');
```

More to come (contributions welcome!).

Locale support for spacing and quotes
-------------------------------------

JoliTypo supports locale-specific rules for spacing before punctuation and quotation marks:

| Locale | Space Before `: ; ! ?` | Quote Style | Nested Quote Style |
|--------|------------------------|-------------|--------------------|
| fr_FR, fr_BE, fr_CH | YES (nbsp/nnbsp) | « text » | “text” |
| fr_CA | NO | « text » | “text” |
| de_DE, de_AT | NO | „text“ | ‚text‘ |
| de_CH | NO | «text» | ‹text› |
| en_* | NO | “text” | ‘text’ |
| es_*, it_*, pt_* | NO | «text» | “text” |
| pl_*, cs_*, sk_*, hu_*, ro_*, bg_* | NO | „text“ | ‚text‘ (pl_*, ro_*: «text») |
| ru_*, uk_*, be_* | NO | «text» | „text“ |
| sv_*, fi_* | NO | "text" | ’text’ |
| nl_*, tr_* | NO | "text" | ‘text’ |

See `LocaleConfig::QUOTE_STYLES_BY_LOCALE` and `LocaleConfig::NESTED_QUOTE_STYLES_BY_LOCALE` for the complete list of supported languages.

Documentation
=============

Default usage
-------------

```php
$fixer        = new Fixer(['Ellipsis', 'Dimension', 'Dash', 'SmartQuotes', 'CurlyQuote', 'Hyphen']);
$fixedContent = $fixer->fix("<p>Some user contributed HTML which does not use proper glyphs.</p>");

$fixer->setRules(['CurlyQuote']);
$fixedContent = $fixer->fix("<p>I'm only replacing single quotes.</p>");

$fixer->setRules(['Hyphen']);
$fixer->setLocale('en_GB'); // I tell which locale to use for Hyphenation and SmartQuotes
$fixedContent = $fixer->fix("<p>Very long words like Antidisestablishmentarianism.</p>");
```

Define your own Fixer
---------------------

If you want to add your own Fixer to the list, you have to implement `JoliTypo\FixerInterface`.
Then just give JoliTypo their fully qualified name, or even instance:

```php
// by FQN
$fixer = new Fixer([
    'Ellipsis', 
    'Acme\\YourOwn\\TypoFixer'
]);
$fixedContent = $fixer->fix("<p>Content fixed by the 2 fixers.</p>");

// or instances, or both
$fixer = new Fixer([
    'Ellipsis', 
    'Acme\\YourOwn\\TypoFixer', 
    new Acme\\YourOwn\\PonyFixer("Some parameter")
]);
$fixedContent = $fixer->fix("<p>Content fixed by the 3 fixers.</p>");
```

Configure the protected tags
----------------------------

Protected tags is a list of HTML tag names that the DOM parser must avoid. Nothing in those tags will be fixed.

```php
$fixer        = new Fixer(['Ellipsis']);
$fixer->setProtectedTags(['pre', 'a']);
$fixedContent = $fixer->fix("<p>Fixed...</p> <pre>Not fixed...</pre> <p>Fixed... <a>Not Fixed...</a>.</p>");
```

Add your own Fixer / Contribute a Fixer
=======================================

- Write tests;
- A Fixer is run on a piece of text, no HTML to deal with;
- Implement `JoliTypo\FixerInterface`;
- Send your Pull request.

### Contribution guidelines

- You MUST write code in English;
- you MUST follow the PER-CS (PSR-12) and Symfony coding standards (run `composer cs` on your branch);
- you MUST run the tests (run `composer test`);
- you MUST comply with the MIT license;
- you SHOULD write documentation.

If you add a new Fixer, please provide sources and references about the typographic rule you want to fix.

Compatibility & OS support restrictions
=======================================

- Windows XP : Thin No-Break Space can't be used, all other spaces are ignored, but they do not look bad (normal space).
- Mac OS Snow Leopard : no no-break space, half no-break space, ems and en-dash but doesn't look bad (normal space).

BUT if you use a font (`@font-face` maybe) that contains all those glyphs, there will be no issues.

There is a known [issue](https://bugs.php.net/bug.php?id=62190) preventing JoliTypo to work correctly with APC versions older than 3.1.11.

What can you do to help?
========================

We need to be able to use this tool everywhere, you can help by providing:
- Wordpress plugin (to replace or complete `wptexturize`)
- Dotclear plugin
...

Also, there is a [Todo list](TODO.md) 😘

License
=======

This piece of code is under MIT License. See the [LICENSE](LICENSE) file.

Alternatives and other implementations
======================================

There is already quite a bunch of tools like this one (including good ones). Sadly, some are only for one language,
some are running regexp on the whole HTML code ([which is bad](http://stackoverflow.com/questions/1732348/regex-match-open-tags-except-xhtml-self-contained-tags/1732454#1732454)), some
are not tested, some are bundled inside a CMS or a Library, some are not using proper auto-loading, some do not have an open bug tracker... Have a look by yourself:

- https://michelf.ca/projets/php-smartypants/
- https://michelf.ca/projets/php-smartypants/typographer/
- http://www.bioinformatics.org/phplabware/internal_utilities/htmLawed/
- https://git.spip.net/spip/ecrire/-/blob/5.x/typographie/fr.php
- https://github.com/dg/texy/blob/master/src/Texy/Modules/TypographyModule.php
- https://github.com/scoates/lexentity
- https://github.com/nofont/Typesetter.js
- https://blot.im/typeset/ (Server side Javascript pre-processor)

Glossary & References
=====================

Thanks to theses online resources for helping a developer understand typography:

- [FR] https://typographisme.net/post/Les-espaces-typographiques-et-le-web
- https://daringfireball.net/projects/smartypants/
- [FR] https://www.uzine.net/article1802.html
- [FR] https://dascritch.net/post/2011/05/09/Les-espacements-unicodes
- https://www.punctuationmatters.com/ **is a must-read**
- https://practicaltypography.com/
- [FR] "Abrégé du code typographique à l'usage de la presse", ISBN: 9782351130667
- https://en.wikipedia.org/wiki/Non-English_usage_of_quotation_marks

Typography rules by language:

- https://type.today/en/journal/spaces - Comprehensive guide on spacing in typography
- https://type.today/en/journal/quotes - Comprehensive guide on quotation marks by language
- https://www.mancko.com/typography-punctuation/en/ - Multi-language typography reference
- [FR] https://fr.wikipedia.org/wiki/Ponctuation#Espaces_et_ponctuation - French punctuation spacing rules

<br><br>
<div align="center">
<a href="https://jolicode.com/"><img src="https://jolicode.com/media/original/oss/footer-github.png?v3" alt="JoliCode is sponsoring this project"></a>
</div>

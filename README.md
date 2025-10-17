<center><a href="https://github.com/jolicode/JoliTypo"><img src="https://jolicode.com/media/original/oss/headers/jolitypo.png" alt="JoliTypo"></a></center>
<center><h1>JoliTypo – Web Microtypography fixer</h1></center>
<center><small>Finally a tool for typography nerds.</small></center>

JoliTypo is a tool fixing [Microtypography](https://en.wikipedia.org/wiki/Microtypography) glitches inside your HTML content.

```php
use JoliTypo\Fixer;

$fixer = new Fixer(['Ellipsis', 'Dash', 'SmartQuotes', 'CurlyQuote', 'Hyphen']);
$fixedContent = $fixer->fix('<p>"Tell me Mr. Anderson... what good is a phone call... if you\'re unable to speak?" -- Agent Smith, <em>Matrix</em>.</p>');
```
```html
<p>&ldquo;Tell me Mr. Ander&shy;son&hellip; what good is a phone call&hellip; if you&rsquo;re unable to speak?&rdquo;&mdash;Agent Smith, <em>Matrix</em>.</p>
```
> “Tell me Mr. Anderson… what good is a phone call… if you’re unable to speak?”—Agent Smith, Matrix.

It's designed to be:

- language agnostic (you can fix `fr_FR`, `fr_CA`, `en_US`... You tell JoliTypo what to fix);
- easy to integrate into modern PHP projects (composer and autoload);
- robust (make use of `\DOMDocument` instead of parsing HTML with dummy regexp);
- smart enough to avoid Javascript, Code, CSS processing... (configurable protected tags list);
- fully tested;
- fully open and usable in any project (MIT License).

You can try it with the [online demo](https://jolitypo.jolicode.com/)!

[![Latest Stable Version](https://poser.pugx.org/jolicode/JoliTypo/version.png)](https://packagist.org/packages/jolicode/JoliTypo)

Quick usage
===========

Just tell the Fixer class [which Fixer](#available-fixers) you want to run on your content and then, call `fix()`:

```php
use JoliTypo\Fixer;

$fixer = new Fixer(["SmartQuotes", "FrenchNoBreakSpace"]);
$fixer->setLocale('fr_FR');
$fixedContent = $fixer->fix('<p>Je suis "très content" de t\'avoir invité sur <a href="http://jolicode.com/">Jolicode.com</a> !</p>');
```

For your ease of use, you can find [ready to use list of Fixer for your language here](#fixer-recommendations-by-locale).
Micro-typography is nothing like a standard or a law, what really matters is consistency, so feel free to use your own lists.

Please be advised that JoliTypo works best on **HTML content**; it will also work on plain text, but will be less smart about
 smart quotes. When fixing a complete HTML document, potential `<head>`, `<html>` and `<body>` tags may be removed.

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

Requirements are handled by Composer (libxml and mbstring are required).

```
composer require jolicode/jolitypo
```

*Usage outside composer is also possible, just add the `src/` directory to any PSR-0 compatible autoloader.*

Integrations
===========

- (Built-in) [Symfony Bundle](src/JoliTypo/Bridge/Symfony)
- (Built-in) [Twig extension](src/JoliTypo/Bridge/Twig)
- (Built-in) [CLI](https://github.com/jolicode/JoliTypo/releases/latest)
- [Wordpress plugin](http://wordpress.org/plugins/typofr/)
- [Drupal module](https://github.com/Anaethelion/JoliTypo-for-Drupal)
- [Joomla plugin](https://github.com/YGomiero/typographe)
- [MODX Extra](https://github.com/jenswittmann/JoliTypo)

Available Fixers
================

Dash
----

Replaces the simple dash `-` by a ndash `–` between numbers (dates ranges...) and the double `--` by a mdash `—`.

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

See [the code](https://github.com/jolicode/JoliTypo/blob/master/src/JoliTypo/Fixer/SmartQuotes.php) for more details,
and do not forget to specify a locale on the Fixer instance.

This Fixer replaces legacy `EnglishQuotes`, `FrenchQuotes` and `GermanQuotes`.

FrenchNoBreakSpace
------------------

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

**Proper hyphenation is mandatory in justified text** and you should avoid word breaking in titles with this line of CSS: `hyphens:none;`.

⚠ Be aware that the current screen readers are unable to spell correctly the words containing `&shy;` tags. The Hyphen filter should therefore be used with caution or you might reduce your website's accessibility.

CurlyQuote (Smart Quote)
-----------------------

Replaces straight quotes `'` with curly ones `’`.
There is one exception to consider: foot and inch marks (minutes and second marks). Purists use prime `′`, this fixer uses straight quotes for compatibility.
[Read more about Curly quotes](http://practicaltypography.com/straight-and-curly-quotes.html).

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
$fixer = new Fixer(['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark']);
$fixer->setLocale('en_GB');
```

fr_FR
-----

Those rules apply for most of the recommendations of "Abrégé du code typographique à l'usage de la presse", ISBN: 9782351130667.

```php
$fixer = new Fixer(['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'FrenchNoBreakSpace', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark']);
$fixer->setLocale('fr_FR');
```

fr_CA
-----

Mostly the same as fr_FR, but the space before punctuation points is not mandatory.

```php
$fixer = new Fixer(['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark']);
$fixer->setLocale('fr_CA');
```

de_DE
-----

Mostly the same as en_GB, according to [Typefacts](http://typefacts.com/) and [Wikipedia](http://de.wikipedia.org/wiki/Typografie_f%C3%BCr_digitale_Texte).

```php
$fixer = new Fixer(['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark']  );
$fixer->setLocale('de_DE');
```

More to come (contributions welcome!).

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
$fixer        = new Fixer(['Ellipsis', 'Acme\\YourOwn\\TypoFixer']);
$fixedContent = $fixer->fix("<p>Content fixed by the 2 fixers.</p>");

// or instances, or both
$fixer        = new Fixer(['Ellipsis', 'Acme\\YourOwn\\TypoFixer', new Acme\\YourOwn\\PonyFixer("Some parameter")]);
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

- You MUST write code in english;
- you MUST follow PSR2 and Symfony coding standard (run `composer cs` on your branch);
- you MUST run the tests (run `composer test`);
- you MUST comply to the MIT license;
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

Also, there is a [Todo list](TODO.md) :kissing_smiling_eyes:

License
=======

This piece of code is under MIT License. See the [LICENSE](LICENSE) file.

Alternatives and other implementations
======================================

There is already quite a bunch of tools like this one (including good ones). Sadly, some are only for one language,
some are running regexp on the whole HTML code ([which is bad](http://stackoverflow.com/questions/1732348/regex-match-open-tags-except-xhtml-self-contained-tags/1732454#1732454)), some
are not tested, some are bundled inside a CMS or a Library, some are not using proper auto-loading, some do not have an open bug tracker... Have a look by yourself:

- http://michelf.ca/projets/php-smartypants/
- http://michelf.ca/projets/php-smartypants/typographer/
- http://www.bioinformatics.org/phplabware/internal_utilities/htmLawed/
- https://github.com/Cerdic/textwheel/blob/master/typographie/fr.php
- https://github.com/spip/SPIP/blob/master/ecrire/typographie/fr.php
- https://github.com/dg/texy/blob/master/Texy/modules/TexyTypographyModule.php
- https://github.com/scoates/lexentity
- https://github.com/nofont/Typesetter.js
- https://github.com/judbd/php-typography (fork of php-typography, you can test it here: http://www.roxane-company.com/typonerd/)
- http://mdash.ru/
- https://blot.im/typeset/ (Server side Javascript pre-processor)

Glossary & References
=====================

Thanks to theses online resources for helping a developer understand typography:

- [FR] http://typographisme.net/post/Les-espaces-typographiques-et-le-web
- http://daringfireball.net/projects/smartypants/
- [FR] http://www.uzine.net/article1802.html
- [FR] http://dascritch.net/post/2011/05/09/Les-espacements-unicodes
- http://www.punctuationmatters.com/ **is a must-read**
- http://practicaltypography.com/
- [FR] "Abrégé du code typographique à l'usage de la presse", ISBN: 9782351130667
- https://en.wikipedia.org/wiki/Non-English_usage_of_quotation_marks

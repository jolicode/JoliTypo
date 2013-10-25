JoliTypo – Web Microtypography fixer
====================================

> Finally a tool for typography nerds.

JoliTypo is a tool fixing [Microtypography](https://en.wikipedia.org/wiki/Microtypography) glitches inside your HTML content.

```php
use JoliTypo\Fixer;

$fixer = new Fixer(array('Ellipsis', 'Dash', 'EnglishQuotes', 'CurlyQuote', 'Hyphen'));
$fixed_content = $fixer->fix('<p>"Tell me Mr. Anderson... what good is a phone call... if you\'re unable to speak?" -- Agent Smith, <em>Matrix</em>.</p>');
```
```html
<p>&ldquo;Tell me Mr. Ander&shy;son&hellip; what good is a phone call&hellip; if you&rsquo;re unable to speak?&rdquo;&mdash;Agent Smith, <em>Matrix</em>.</p>
```
> “Tell me Mr. Anderson… what good is a phone call… if you’re unable to speak?”—Agent Smith, Matrix.

It's designed to be:

- language agnostic (you can fix `fr_FR`, `fr_CA`, `en_US`... You tell JoliTypo what to fix);
- fully tested;
- easy to integrate into modern PHP project (composer and autoload);
- robust (make use of `\DOMDocument` instead of parsing HTML with dummy regexp);
- smart enough to avoid Javascript, Code, CSS processing... (configurable protected tags list);
- fully open and usable in any project (MIT License).

**This software is still in alpha, some Fixer are missing for a proper release, and everything can change.**

[![Build Status](https://travis-ci.org/jolicode/JoliTypo.png?branch=master)](https://travis-ci.org/jolicode/JoliTypo)
[![Latest Stable Version](https://poser.pugx.org/jolicode/JoliTypo/version.png)](https://packagist.org/packages/jolicode/JoliTypo)
[![Latest Unstable Version](https://poser.pugx.org/jolicode/JoliTypo/v/unstable.png)](https://packagist.org/packages/jolicode/JoliTypo)
[![Code Coverage](https://scrutinizer-ci.com/g/jolicode/JoliTypo/badges/coverage.png?s=4158f94c2d7ee660eebf562baa1c6cf8f1152784)](https://scrutinizer-ci.com/g/jolicode/JoliTypo/)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/1e8d7acc-dfad-497b-a2aa-db1223bbf684/mini.png)](https://insight.sensiolabs.com/projects/1e8d7acc-dfad-497b-a2aa-db1223bbf684)
[![Dependency Status](https://www.versioneye.com/php/jolicode:jolitypo/dev-master/badge.png)](https://www.versioneye.com/php/jolicode:jolitypo/dev-master)
*I love badges ^^*

Quick usage
===========

Just tell the Fixer class [which Fixer](#available-fixers) you want to run on your HTML content and then, call `fix()`:

```php
use JoliTypo\Fixer;

$fixer = new Fixer(array("FrenchQuotes", "FrenchNoBreakSpace"));
$fixed_content = $fixer->fix('<p>Je suis "très content" de t\'avoir invité sur <a href="http://jolicode.com/">Jolicode.com</a> !</p>');
```

For your ease of use, you can find [ready to use list of Fixer for your language here](#fixer-recommendations-by-locale).
Micro-typography is nothing like a standard or a law, what really matter is consistency, so feel free to use your own lists.

Installation
============

```
composer require jolicode/jolitypo dev-master
```

*Usage outside composer is also possible, just add the `src/` directory to any PSR-0 compatible autoloader.*

We also provide a [Symfony2 Bundle with a Twig extension](https://github.com/jolicode/JoliTypoBundle).

Available Fixers
================

Dash
----

Replace the simple `-` by a ndash `–` between numbers (dates ranges...) and the double `--` by a mdash `—`.

Dimension
---------

Replace the letter x between numbers (`12 x 123`) by a times entity (`×`, the real math symbol).

Ellipsis
--------

Replace the three dot `...` by an ellipsis `…`.

EnglishQuotes
-------------

Convert dumb quotes `" "` to smart English style quotation marks `“ ”`.

FrenchQuotes
------------

Convert dumb quotes `" "` to smart French style quotation marks `« »` and use a no break space.

GermanQuotes
------------

Convert dumb quotes `" "` to smart German style quotation marks `„ “` (Anführungszeichen).
Some fonts (Verdana) are typographically incompatible with German.

FrenchNoBreakSpace
------------------

Replace some classic spaces by non breaking spaces following the French typographic code.
No break space are placed before `:`, thin no break space before `;`, `!` and `?`.

Hyphen (automatic hyphenation)
------------------------------

Make use of `org_heigl/hyphenator`, a tool enabling word-hyphenation in PHP.
This Hyphenator uses the pattern-files from OpenOffice which are based on the pattern-files created for TeX.

There is only some locale available for this fixer: af_ZA, ca, da_DK, de_AT, de_CH, de_DE, en_GB, en_UK, et_EE, fr, hr_HR, hu_HU, it_IT, lt_LT, nb_NO, nn_NO, nl_NL, pl_PL, pt_BR, ro_RO, ru_RU, sk_SK, sl_SI, sr, zu_ZA.

You can read more about this fixer on [the official github repository](https://github.com/heiglandreas/Org_Heigl_Hyphenator).

**This Fixer require a Locale to be set on the Fixer with `$fixer->setLocale('fr_FR');`. Default to `en_GB`.**

**Hyphenation is mandatory in justified text** and you should avoid word breaking in titles with this line of CSS: `hyphens:none;`.

CurlyQuote (Smart Quote)
-----------------------

Replace straight quotes `'` by curly one's `’`.
There is on exception to consider: foot and inch marks (minutes and second marks). Purists use prime `′`, this fixer use straight quote for compatibility.
[Read more about Curly quotes](http://practicaltypography.com/straight-and-curly-quotes.html).

Trademark
---------

Handle trade­mark symbol `™`, a reg­is­tered trade­mark symbol `®`, and a copy­right symbol `©`. This fixer replace
commonly used approximations: `(r)`, `(c)` and `(TM)`. A non-breaking space is put between numbers and copyright symbol too.

**It is really easy to make your own Fixers, feel free to extend the provided ones if they do not fit your typographic rules.**

Fixer recommendations by locale
===============================

en_GB
-----

```php
$fixer = new Fixer(array('Ellipsis', 'Dimension', 'Dash', 'EnglishQuotes', 'CurlyQuote', 'Hyphen', 'Trademark'));
$fixer->setLocale('en_GB'); // Needed by the Hyphen Fixer
```

fr_FR
-----

Those rules apply most of the recommendations of "Abrégé du code typographique à l'usage de la presse", ISBN: 9782351130667.

```php
$fixer = new Fixer(array('Ellipsis', 'Dimension', 'Dash', 'FrenchQuotes', 'FrenchNoBreakSpace', 'CurlyQuote', 'Hyphen', 'Trademark'));
$fixer->setLocale('fr_FR'); // Needed by the Hyphen Fixer
```

fr_CA
-----

Mostly the same as fr_FR, but the space before punctuation points is not mandatory.

```php
$fixer = new Fixer(array('Ellipsis', 'Dimension', 'Dash', 'FrenchQuotes', 'CurlyQuote', 'Hyphen', 'Trademark'));
$fixer->setLocale('fr_CA'); // Needed by the Hyphen Fixer
```

de_DE
-----

Mostly the same as en_GB, according to [Typefacts](http://typefacts.com/) and [Wikipedia](http://de.wikipedia.org/wiki/Typografie_f%C3%BCr_digitale_Texte).

```php
$fixer = new Fixer(array('Ellipsis', 'Dimension', 'Dash', 'GermanQuotes', 'CurlyQuote', 'Hyphen', 'Trademark'));
$fixer->setLocale('de_DE'); // Needed by the Hyphen Fixer
```

More to come (contributions welcome!).


Documentation
=============

Default usage
-------------

```php
$fixer          = new Fixer(array('Ellipsis', 'Dimension', 'Dash', 'EnglishQuotes', 'CurlyQuote', 'Hyphen'));
$fixed_content  = $fixer->fix("<p>Some user contributed HTML which does not use proper glyphs.</p>");

$fixer->setRules(array('CurlyQuote'));
$fixed_content = $fixer->fix("<p>I'm only replacing single quotes.</p>");

$fixer->setRules(array('Hyphen'));
$fixer->setLocale('en_GB'); // I tell which locale to use for Hyphenation
$fixed_content = $fixer->fix("<p>Very long words like Antidisestablishmentarianism.</p>");
```

Define your own Fixer
---------------------

If you want to add your own Fixer to the list, you have to implement `JoliTypo\FixerInterface`.
Then just give JoliTypo their fully qualified name, or even instance:

```php
// by FQN
$fixer          = new Fixer(array('Ellipsis', 'Acme\\YourOwn\\TypoFixer'));
$fixed_content  = $fixer->fix("<p>Content fixed by the 2 fixers.</p>");

// or instances, or both
$fixer          = new Fixer(array('Ellipsis', 'Acme\\YourOwn\\TypoFixer', new Acme\\YourOwn\\PonyFixer("Some parameter")));
$fixed_content  = $fixer->fix("<p>Content fixed by the 3 fixers.</p>");
```

Configure the protected tags
----------------------------

Protected tags is a list of HTML tag name that the DOM parser must avoid. Nothing in those tags will be fixed.

```php
$fixer          = new Fixer(array('Ellipsis'));
$fixer->setProtectedTags(array('pre', 'a'));
$fixed_content  = $fixer->fix("<p>Fixed...</p> <pre>Not fixed...</pre> <p>Fixed... <a>Not Fixed...</a>.</p>");
```

Add your own Fixer / Contribute a Fixer
=======================================

- Write test
- A Fixer is run on a piece of text, no HTML to deal with
- Implement `JoliTypo\FixerInterface`
- Pull request
- PROFIT!!!

Compatibility & OS support restrictions
=======================================

- Windows XP : Thin No-Break Space can't be used, all other spaces are ignored but they do not look bad (normal space).
- Mac OS Snow Leopard : no espaces fixes, demi-fixes, cadratin et demi-cadratin but does not look bad (normal space).

BUT if you use a font (`@font-face` maybe) that contains all those glyphs, there will be no issues.

There is a known [issue](https://bugs.php.net/bug.php?id=62190) preventing JoliTypo to work corectly with APC versions older than 3.1.11.

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

There are already quite a bunch of tool like this one (including good ones). But sadly, some are only for one language,
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

Glossary & References
=====================

Thanks to theses online resources for helping a developer understand typography:

- [FR] http://typographisme.net/post/Les-espaces-typographiques-et-le-web
- http://daringfireball.net/projects/smartypants/
- [FR] http://www.uzine.net/article1802.html
- [FR] http://dascritch.net/post/2011/05/09/Les-espacements-unicodes
- http://www.punctuationmatters.com/ **is a must read**
- http://practicaltypography.com/
- [FR] "Abrégé du code typographique à l'usage de la presse", ISBN: 9782351130667
- https://en.wikipedia.org/wiki/Non-English_usage_of_quotation_marks

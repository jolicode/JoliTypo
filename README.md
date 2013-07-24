JoliTypo – Web Microtypography fixer
====================================

> Finally a tool for typography nerds.

JoliTypo is a tool fixing [Microtypography](https://en.wikipedia.org/wiki/Microtypography) glitches inside your HTML content.

```php
use JoliTypo\Fixer;

$fixer = new Fixer("FrenchQuote", "FrenchNoBreakSpace", "SingleQuote");
$fixed_content = $fixer->fix('<p>Je suis "très content" de t\'avoir invité sur <a href="http://jolicode.com/">Jolicode.com</a> !</p>');
```
```html
<p>Je suis &laquo;&nbsp;tr&egrave;s content&nbsp;&raquo; de t&rsquo;avoir invit&eacute; sur <a href="http://jolicode.com/">Jolicode.com</a>&#8239;!</p>
```
> Je suis « très content » de t’avoir invité sur Jolicode.com !

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
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/1e8d7acc-dfad-497b-a2aa-db1223bbf684/small.png)](https://insight.sensiolabs.com/projects/1e8d7acc-dfad-497b-a2aa-db1223bbf684)
*I love badges ^^*

Quick usage
===========

Just tell the Fixer class [which Fixer](#available-fixers) you want to run on your HTML content and then, call `fix()`:

```php
use JoliTypo\Fixer;

$fixer = new Fixer(array("FrenchQuote", "FrenchNoBreakSpace"));
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

Available Fixers
================

Dash
----

Replace the simple `-` by a ndash (–) between numbers (dates ranges...) and the double `--` by a mdash (—).

Dimension
---------

Replace the letter x between numbers (`12 x 123`) by a times entity (×).

Ellipsis
--------

Replace the three dot (`...`) by an ellipsis (…).

EnglishQuotes
-------------

Convert dumb quotes (`" "`) to smart quotes (“ ”).

FrenchNoBreakSpace
------------------

Replace some classic spaces by non breaking spaces following the French typographic code.
No break space are placed before `:`, thin no break space before `;`, `!` and `?`.

FrenchQuote
-----------

Convert dumb quotes (`" "`) to French quotes (« ») and use a no break space.

Hyphen
------

Make use of `org_heigl/hyphenator`, a tool enabling word-hyphenation in PHP.
This Hyphenator uses the pattern-files from OpenOffice which are based on the pattern-files created for TeX.

There is only some locale available for this fixer: af_ZA, ca, da_DK, de_AT, de_CH, de_DE, en_GB, en_UK, et_EE, fr, hr_HR, hu_HU, it_IT, lt_LT, nb_NO, nn_NO, nl_NL, pl_PL, pt_BR, ro_RO, ru_RU, sk_SK, sl_SI, sr, zu_ZA.

You can read more about this fixer on [the official github repository](https://github.com/heiglandreas/Org_Heigl_Hyphenator).

**This Fixer require a Locale to be set on the Fixer with `$fixer->setLocale('fr_FR');`. Default to `en_GB`.**

**Hyphenation is only a good idea if your texts are justified.**

SingleQuote
-----------

Replace all the quotes (`'`) by a real rsquo (’).




**It is really easy to make your own Fixers, feel free to extend the provided ones if they do not fit your typographic rules.**

Fixer recommendations by locale
===============================

en_GB
-----

```php
$fixer = new Fixer(array('Ellipsis', 'Dimension', 'Dash', 'EnglishQuotes', 'SingleQuote', 'Hyphen'));
$fixer->setLocale('en_GB'); // Needed by the Hyphen Fixer
```

fr_FR
-----

Those rules apply most of the recommendations of "Abrégé du code typographique à l'usage de la presse", ISBN: 9782351130667.

```php
$fixer = new Fixer(array('Ellipsis', 'Dimension', 'Dash', 'FrenchQuotes', 'FrenchNoBreakSpace', 'SingleQuote', 'Hyphen'));
$fixer->setLocale('fr_FR'); // Needed by the Hyphen Fixer
```

fr_CA
-----

Mostly the same as fr_FR, but the space before punctuation points is not mandatory.

```php
$fixer = new Fixer(array('Ellipsis', 'Dimension', 'Dash', 'FrenchQuotes', 'SingleQuote', 'Hyphen'));
$fixer->setLocale('fr_CA'); // Needed by the Hyphen Fixer
```

More to come (contributions welcome!).


Documentation
=============

Default usage
-------------

```php
$fixer          = new Fixer(array('Ellipsis', 'Dimension', 'Dash', 'EnglishQuotes', 'SingleQuote', 'Hyphen'));
$fixed_content  = $fixer->fix("<p>Some user contributed HTML which does not use proper glyphs.</p>");

$fixer->setRules(array('SingleQuote'));
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

Todo / Rules to be developed
============================

Global
------

- Add results on ALL the documentation examples
- Should we run the fixes on `title` attributes and image `alt`?
- Add a HTML entities to UTF-8 converter?
- Improve the EnglishTest
- Add more pre-configured locale to `Fixer.php`, with the appropriate test
- Provide a Twig filter? (will be in a dedicated Bundle)
- Add a http://cldr.unicode.org/ Fixer for number formatting (thx @g_marty for the tip!)
- Do not call setLocale on construct (lazy load the rules)
- In setLocale, if $this->locale === $locale, do not recompute the rules

fr-FR
-----

- Numbers > 1000 must be separated by groups of 3 with a non breaking space (1000 => 1 000, 1013424 => 1 013 424)
- Quotes and double-quotes inside FrenchQuotes should be translated to english quotes:

    > Il nous raconta : « Hier, je me promenais sur les quais. Je demandai à un passant : “Quelle heure est-il ?”
    > Il répondit : “Désolé, je n’ai pas de montre, il doit être midi, mais c’est ‛sans garantie’.” Je le remerciai et partis. »

fr-CA
-----

- Same as French but ignore space before `;!?`

fr-CH
-----

- Same as French but Narrow No-Break Space before `:`

en-GB
-----

- Make sure all the rules described [here](http://practicaltypography.com/summary-of-key-rules.html) are respected


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

License
=======

This piece of code is under MIT License. See the LICENSE file.

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
- https://github.com/judbd/php-typography (fork of http://kingdesk.com/projects/php-typography/)

Glossary & References
=====================

Thanks to theses online resources for helping a developer understand typography:

- [FR] http://typographisme.net/post/Les-espaces-typographiques-et-le-web
- http://daringfireball.net/projects/smartypants/
- [FR] http://www.uzine.net/article1802.html
- [FR] http://dascritch.net/post/2011/05/09/Les-espacements-unicodes
- http://www.punctuationmatters.com/
- http://practicaltypography.com/
- "Abrégé du code typographique à l'usage de la presse", ISBN: 9782351130667

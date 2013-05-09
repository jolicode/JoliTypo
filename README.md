JoliTypo - Microtypography fixer for the web
============================================

> Finally a tool for typography nerds.

JoliTypo is a tool fixing [Microtypography](https://en.wikipedia.org/wiki/Microtypography) glitches inside your HTML content.

    $fixer = new Fixer('en_GB');
    $fixed_content = $fixer->fix('<p>Some user contributed HTML which does not use proper glyphs</p>");

It's designed to be:

- language agnostic (you can fix `fr_FR`, `fr_CA` and `en_US` there own ways)
- fully tested
- easy to integrate into modern PHP project (composer and autoload)
- robust (make use of `\DOMDocument` instead of parsing HTML with dummy regexp)
- fast
- fully open and usable in any project (MIT Licence)

Available Fixer
===============

// @todo

How to use
==========

// @todo

Todo / Rules to be developed
============================

Global
------

- Hyphenator?

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


Compatibility & OS support restrictions
=======================================

- Windows XP : Narrow No-Break Space can't be used, all other spaces are ignored but they do not look bad (normal space).
- Mac OS Snow Leopard : no espaces fixes, demi-fixes, cadratin et demi-cadratin but does not look bad (normal space).

BUT if you use a font (`@font-face` maybe) that contains all thoses glyphs, there will be no issues.

Glossary & References
=====================

Thanks to theses online resources for helping a developer understands typography:

- [FR] http://typographisme.net/post/Les-espaces-typographiques-et-le-web
- http://daringfireball.net/projects/smartypants/
- [FR] http://www.uzine.net/article1802.html
- [FR] http://dascritch.net/post/2011/05/09/Les-espacements-unicodes
- http://www.punctuationmatters.com/

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

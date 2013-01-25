

Rules to apply
==============


fr-FR rules
-----------


fr-CA rules
-----------

- Same as French but ignore space before `;!?`


fr-CH
-----

- Same as French but Narrow No-Break Space before `:`


Compatibility & OS support restrictions
=======================================


- Windows XP : Narrow No-Break Space can't be used, all other spaces are ignored but they do not look bad (normal space).
- Mac OS Snow Leopard : no espaces fixes, demi-fixes, cadratin et demi-cadratin but does not look bad (normal space).

BUT if you use a font (@font-face maybe) that contains all thoses glyph, there will be no issues.


Bullet-proof mode
=================

Made to be less compliant but fully compatible with un-complete fonts or un-capable OS/Browsers.

 - replace Narrow No-Break Space by No-Break Space

Glossary & References
=====================

- Narrow No-Break Space (espace fine insécable): `U+202F`, `&#8239;`


Thanks to theses online resources for helping a developer understands typography:

- http://typographisme.net/post/Les-espaces-typographiques-et-le-web

- http://daringfireball.net/projects/smartypants/

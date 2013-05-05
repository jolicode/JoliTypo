Todo / Rules to apply
=====================


fr-FR rules
-----------

- Numbers > 1000 must be separated by groups of 3 with a non breaking space (1000 => 1 000, 1013424 => 1 013 424)

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


Glossary & References
=====================

- Narrow No-Break Space (espace fine insécable): `U+202F`, `&#8239;`


Thanks to theses online resources for helping a developer understands typography:

- http://typographisme.net/post/Les-espaces-typographiques-et-le-web

- http://daringfireball.net/projects/smartypants/

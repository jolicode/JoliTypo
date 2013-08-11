Todo on JoliTypo
===============

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

Locale to improve
=================

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



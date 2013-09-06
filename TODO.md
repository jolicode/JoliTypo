Todo on JoliTypo
===============

Global
------

- [ ] Add results on ALL the documentation examples for teaching purpose;
- [ ] Investigate if we hould run the fixers on `title` attributes and image `alt`;
- [x] Improve the EnglishTest;
- [ ] Add more pre-configured locale in the README, with the appropriate tests;
- [ ] Add a http://cldr.unicode.org/ Fixer for number formatting (thx @g_marty for the tip!);
- [ ] A website in with you copy some HTML and get a fixed HTML outputted;
- [ ] Allow to apply thoses rules: http://en.wikipedia.org/wiki/Non-English_usage_of_quotation_marks#Hungarian and document them

Locale to improve
=================

All
---

- [ ] Include a non breaking space in the middle of a foot-and-inch measurement (6' 10") to avoid awkwardness (new Fixer?);

fr-FR
-----

- [ ] Numbers > 1000 must be separated by groups of 3 with a non breaking space (1000 => 1 000, 1013424 => 1 013 424);
- [ ] Quotes and double-quotes inside FrenchQuotes should be translated to english quotes:
    > Il nous raconta : « Hier, je me promenais sur les quais. Je demandai à un passant : “Quelle heure est-il ?”
    > Il répondit : “Désolé, je n’ai pas de montre, il doit être midi, mais c’est ‛sans garantie’.” Je le remerciai et partis. »

es-ES
-----

- [ ] ¿¡_inverted_!? exclamation and question marks.

fr-CA
-----

- [x] Same as French but ignore space before `;!?`, pre-configuration to add on the README;

fr-CH
-----

- [ ] Same as French but Narrow No-Break Space before `:`, how to handle this?

en-GB
-----

- [x] Make sure all the rules described [here](http://practicaltypography.com/summary-of-key-rules.html) are respected;

CHANGELOG
=========

### 1.3.0 (not released yet) ###

* Add PHAR generation to use the library in CLI context
* Drop support for PHP < 7.4

### 1.2.0 (2021-08-30) ###

* Switch from Travis to GitHub Action
* Bump `org_heigl/hyphenator` to 2.6.1
* Drop support for PHP < 7.2

### 1.1.0 (2020-04-11) ###

* drop Symfony 2 support for the Bundle
* add Symfony 5 support for the Bundle
* better tests
* fix a full DOM dump error occurring on Ubuntu PHP 7.3

### 1.0.5 (2019-07-01) ###

* add an exception for "de-ch" SmartQuote style
* fix deprecation messages in Symfony

### 1.0.4 (2018-11-26) ###

* fix Bundle namespace
* PHP minimum required version is now 5.6

### 1.0.3 (2018-09-04) ###

* add built-in support for Symfony and Twig

### 1.0.2 (2017-03-31) ###

* fix PHP CS Fixer configuration and version
* fix #30 deprecate the PHP 7 reserved word Numeric class in favor of Unit

### 1.0.1 (2015-12-13) ###

* fix #28, NoSpaceBeforeComma should not change spaces inside digits

### 1.0 (2015-12-13) ###

* add Numeric fixer, adding NoBreakSpace between numeric and their units, fix #15
* PSR2 and Symfony coding standard applied everywhere
* add `fixString` method to bypass the DomDocument parsing and directly run the fixers on a string
* better handling of common spaces in texts to fix (do not use `\s` anymore)
* :warning: replace `EnglishQuotes`, `FrenchQuotes`, `GermanQuotes` by the new `SmartQuotes`! A BC layer is provided.

### 0.2.0 (2015-07-13) ###

* new NoSpaceBeforeComma fixer, cleaning badly placed spaces close to commas
* add NoBreakSpace inside french quotes automatically

### 0.1.4 (2014-06-17) ###

* add HHVM tests on travis
* add libxml to composer requirements
* set APC 3.1.11 as conflict (https://bugs.php.net/bug.php?id=62190)
* do not process empty contents
* apply `mb_convert_encoding($content, 'HTML-ENTITIES', $encoding)` on all contents to fix encoding
* workaround for old (2.6.32) libxml versions (#7)
* better Org_Heigl_Hyphenator version requirement

### 0.1.3 (2013-11-15) ###

* test on PHP 5.5 ok
* better error messages
* fix edge case when the opening quote in right after an `(`
* regexp was capturing UTF8 sequence, some octets were eaten in some cases. Added the "u" modifier to disable this feature from the preg_replace function

### 0.1.2 (2013-09-10) ###

* Fix Org_Heigl_Hyphenator version to >= v2.0.3 (issue with spaces in the previous ones)

### 0.1.1 (2013-09-06) ###

* Fix double quote mess on complex DOM, the sibling fixer was runned after the simple regexp,
so his own regexp were useless
* `fixViaState` only replace the first matching occurrence, fix issue on multi-lines DOMText
* Add GermanQuotes fixer

### 0.1 (2013-08-29) ###

* Initial release, ready for test and production

CHANGELOG
=========

### ??? ###

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

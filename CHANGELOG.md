CHANGELOG
=========

### 0.1... (????-??-??) ###

### 0.1.2 (2013-09-??) ###

* Fix Org_Heigl_Hyphenator version to >= v2.0.3 (issue with spaces in the previous ones)

### 0.1.1 (2013-09-06) ###

* Fix double quote mess on complex DOM, the sibling fixer was runned after the simple regexp,
so his own regexp were useless
* `fixViaState` only replace the first matching occurrence, fix issue on multi-lines DOMText
* Add GermanQuotes fixer

### 0.1 (2013-08-29) ###

* Initial release, ready for test and production

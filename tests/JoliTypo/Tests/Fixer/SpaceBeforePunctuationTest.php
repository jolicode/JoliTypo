<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo\Tests\Fixer;

use JoliTypo\Fixer;
use PHPUnit\Framework\TestCase;

class SpaceBeforePunctuationTest extends TestCase
{
    public function testFrenchLocale(): void
    {
        $fixer = new Fixer\SpaceBeforePunctuation('fr_FR');
        $this->assertInstanceOf(Fixer\SpaceBeforePunctuation::class, $fixer);

        // Exclamation mark: should use NO_BREAK_THIN_SPACE
        $this->assertSame('Superman' . Fixer::NO_BREAK_THIN_SPACE . '!', $fixer->fix('Superman !'));

        // Question mark: should use NO_BREAK_THIN_SPACE
        $this->assertSame('Superman' . Fixer::NO_BREAK_THIN_SPACE . '?', $fixer->fix('Superman ?'));

        // Multiple punctuation marks
        $this->assertSame('Superman' . Fixer::NO_BREAK_THIN_SPACE . '!?', $fixer->fix('Superman !?'));
        $this->assertSame('Superman' . Fixer::NO_BREAK_THIN_SPACE . '? Nope.', $fixer->fix('Superman ? Nope.'));

        // Colon: should use NO_BREAK_SPACE
        $this->assertSame('Superman' . Fixer::NO_BREAK_SPACE . ': the movie', $fixer->fix('Superman : the movie'));

        // Colon without space before: should not be modified (preserves URLs, times, etc.)
        $this->assertSame('Superman: the movie', $fixer->fix('Superman: the movie'));

        // Semicolon: should use NO_BREAK_THIN_SPACE
        $this->assertSame('Superman' . Fixer::NO_BREAK_THIN_SPACE . '; the movie', $fixer->fix('Superman ; the movie'));

        // Replace existing nbsp with correct space
        $this->assertSame('Superman' . Fixer::NO_BREAK_THIN_SPACE . '; the movie', $fixer->fix("Superman\u{a0}; the movie"));

        // French guillemets
        $this->assertSame(Fixer::LAQUO . Fixer::NO_BREAK_SPACE . 'test' . Fixer::NO_BREAK_SPACE . Fixer::RAQUO, $fixer->fix('« test »'));
        $this->assertSame(Fixer::LAQUO . Fixer::NO_BREAK_SPACE . 'test' . Fixer::NO_BREAK_SPACE . Fixer::RAQUO, $fixer->fix('«test»'));
    }

    public function testFrenchLocaleEdgeCases(): void
    {
        $fixer = new Fixer\SpaceBeforePunctuation('fr_FR');

        // IPv6 addresses should not be modified
        $this->assertSame('fdda:5cc1:23:4::1f', $fixer->fix('fdda:5cc1:23:4::1f'));

        // Brand names with exclamation should not add space if none exists
        $this->assertSame('Here is a  brand name: Yahoo!', $fixer->fix('Here is a  brand name: Yahoo!'));
    }

    public function testEnglishLocale(): void
    {
        $fixer = new Fixer\SpaceBeforePunctuation('en_GB');

        // English should remove spaces before punctuation
        $this->assertSame('Hello!', $fixer->fix('Hello !'));
        $this->assertSame('Hello?', $fixer->fix('Hello ?'));
        $this->assertSame('Hello;', $fixer->fix('Hello ;'));
        $this->assertSame('Hello:', $fixer->fix('Hello :'));

        // No space should remain unchanged
        $this->assertSame('Hello!', $fixer->fix('Hello!'));
        $this->assertSame('Hello?', $fixer->fix('Hello?'));
    }

    public function testEnglishLocaleEdgeCases(): void
    {
        $fixer = new Fixer\SpaceBeforePunctuation('en_GB');

        // URLs should not be modified
        $this->assertSame('http://example.com', $fixer->fix('http://example.com'));
        $this->assertSame('https://example.com', $fixer->fix('https://example.com'));

        // Time format should not be modified (no space before colon)
        $this->assertSame('10:30', $fixer->fix('10:30'));

        // IPv6 should not be modified (no space before colons)
        $this->assertSame('fdda:5cc1::1f', $fixer->fix('fdda:5cc1::1f'));
    }

    public function testCanadianFrenchLocale(): void
    {
        $fixer = new Fixer\SpaceBeforePunctuation('fr_CA');

        // Canadian French behaves like English: no space before punctuation
        $this->assertSame('Bonjour!', $fixer->fix('Bonjour !'));
        $this->assertSame('Bonjour?', $fixer->fix('Bonjour ?'));
        $this->assertSame('Bonjour;', $fixer->fix('Bonjour ;'));
        $this->assertSame('Bonjour:', $fixer->fix('Bonjour :'));
    }

    public function testGermanLocale(): void
    {
        $fixer = new Fixer\SpaceBeforePunctuation('de_DE');

        // German: no space before punctuation
        $this->assertSame('Hallo!', $fixer->fix('Hallo !'));
        $this->assertSame('Hallo?', $fixer->fix('Hallo ?'));
        $this->assertSame('Hallo;', $fixer->fix('Hallo ;'));
        $this->assertSame('Hallo:', $fixer->fix('Hallo :'));
    }

    public function testSwissGermanLocale(): void
    {
        $fixer = new Fixer\SpaceBeforePunctuation('de_CH');

        // Swiss German: no space before punctuation (like German)
        $this->assertSame('Hallo!', $fixer->fix('Hallo !'));
        $this->assertSame('Hallo?', $fixer->fix('Hallo ?'));

        // But Swiss German uses French-style guillemets with thin spaces
        $this->assertSame(Fixer::LAQUO . Fixer::NO_BREAK_THIN_SPACE . 'test' . Fixer::NO_BREAK_THIN_SPACE . Fixer::RAQUO, $fixer->fix('« test »'));
        $this->assertSame(Fixer::LAQUO . Fixer::NO_BREAK_THIN_SPACE . 'test' . Fixer::NO_BREAK_THIN_SPACE . Fixer::RAQUO, $fixer->fix('«test»'));
    }

    public function testSpanishLocale(): void
    {
        $fixer = new Fixer\SpaceBeforePunctuation('es_ES');

        // Spanish: no space before punctuation
        $this->assertSame('Hola!', $fixer->fix('Hola !'));
        $this->assertSame('Hola?', $fixer->fix('Hola ?'));
    }

    public function testItalianLocale(): void
    {
        $fixer = new Fixer\SpaceBeforePunctuation('it_IT');

        // Italian: no space before punctuation
        $this->assertSame('Ciao!', $fixer->fix('Ciao !'));
        $this->assertSame('Ciao?', $fixer->fix('Ciao ?'));
    }

    public function testLocaleCanBeChanged(): void
    {
        $fixer = new Fixer\SpaceBeforePunctuation('en_GB');

        // English: remove space
        $this->assertSame('Hello!', $fixer->fix('Hello !'));

        // Change to French
        $fixer->setLocale('fr_FR');
        $this->assertSame('Bonjour' . Fixer::NO_BREAK_THIN_SPACE . '!', $fixer->fix('Bonjour !'));

        // Change back to English
        $fixer->setLocale('en_GB');
        $this->assertSame('Hello!', $fixer->fix('Hello !'));
    }

    public function testBelgianFrenchLocale(): void
    {
        $fixer = new Fixer\SpaceBeforePunctuation('fr_BE');

        // Belgian French follows French rules
        $this->assertSame('Bonjour' . Fixer::NO_BREAK_THIN_SPACE . '!', $fixer->fix('Bonjour !'));
        $this->assertSame('Bonjour' . Fixer::NO_BREAK_SPACE . ': test', $fixer->fix('Bonjour : test'));
    }

    public function testSwissFrenchLocale(): void
    {
        $fixer = new Fixer\SpaceBeforePunctuation('fr_CH');

        // Swiss French follows French rules
        $this->assertSame('Bonjour' . Fixer::NO_BREAK_THIN_SPACE . '!', $fixer->fix('Bonjour !'));
        $this->assertSame('Bonjour' . Fixer::NO_BREAK_SPACE . ': test', $fixer->fix('Bonjour : test'));
    }

    public function testUnknownLocaleFallsBackToDefault(): void
    {
        $fixer = new Fixer\SpaceBeforePunctuation('xx_XX');

        // Unknown locale: should remove spaces (default behavior)
        $this->assertSame('Hello!', $fixer->fix('Hello !'));
        $this->assertSame('Hello?', $fixer->fix('Hello ?'));
    }

    // =========================================================================
    // Nordic languages
    // =========================================================================

    public function testSwedishLocale(): void
    {
        $fixer = new Fixer\SpaceBeforePunctuation('sv_SE');

        $this->assertSame('Hej!', $fixer->fix('Hej !'));
        $this->assertSame('Hej?', $fixer->fix('Hej ?'));
        $this->assertSame('Hej;', $fixer->fix('Hej ;'));
        $this->assertSame('Hej:', $fixer->fix('Hej :'));
    }

    public function testDanishLocale(): void
    {
        $fixer = new Fixer\SpaceBeforePunctuation('da_DK');

        $this->assertSame('Hej!', $fixer->fix('Hej !'));
        $this->assertSame('Hej?', $fixer->fix('Hej ?'));
    }

    public function testNorwegianLocale(): void
    {
        $fixer = new Fixer\SpaceBeforePunctuation('nb_NO');

        $this->assertSame('Hei!', $fixer->fix('Hei !'));
        $this->assertSame('Hei?', $fixer->fix('Hei ?'));
    }

    public function testFinnishLocale(): void
    {
        $fixer = new Fixer\SpaceBeforePunctuation('fi_FI');

        $this->assertSame('Hei!', $fixer->fix('Hei !'));
        $this->assertSame('Hei?', $fixer->fix('Hei ?'));
    }

    // =========================================================================
    // Slavic languages
    // =========================================================================

    public function testPolishLocale(): void
    {
        $fixer = new Fixer\SpaceBeforePunctuation('pl_PL');

        $this->assertSame('Cześć!', $fixer->fix('Cześć !'));
        $this->assertSame('Cześć?', $fixer->fix('Cześć ?'));
        $this->assertSame('Cześć;', $fixer->fix('Cześć ;'));
        $this->assertSame('Cześć:', $fixer->fix('Cześć :'));
    }

    public function testRussianLocale(): void
    {
        $fixer = new Fixer\SpaceBeforePunctuation('ru_RU');

        $this->assertSame('Привет!', $fixer->fix('Привет !'));
        $this->assertSame('Привет?', $fixer->fix('Привет ?'));
        $this->assertSame('Привет;', $fixer->fix('Привет ;'));
        $this->assertSame('Привет:', $fixer->fix('Привет :'));
    }

    public function testUkrainianLocale(): void
    {
        $fixer = new Fixer\SpaceBeforePunctuation('uk_UA');

        $this->assertSame('Привіт!', $fixer->fix('Привіт !'));
        $this->assertSame('Привіт?', $fixer->fix('Привіт ?'));
    }

    public function testCzechLocale(): void
    {
        $fixer = new Fixer\SpaceBeforePunctuation('cs_CZ');

        $this->assertSame('Ahoj!', $fixer->fix('Ahoj !'));
        $this->assertSame('Ahoj?', $fixer->fix('Ahoj ?'));
    }

    // =========================================================================
    // Other European languages
    // =========================================================================

    public function testPortugueseLocale(): void
    {
        $fixer = new Fixer\SpaceBeforePunctuation('pt_PT');

        $this->assertSame('Olá!', $fixer->fix('Olá !'));
        $this->assertSame('Olá?', $fixer->fix('Olá ?'));
    }

    public function testDutchLocale(): void
    {
        $fixer = new Fixer\SpaceBeforePunctuation('nl_NL');

        $this->assertSame('Hallo!', $fixer->fix('Hallo !'));
        $this->assertSame('Hallo?', $fixer->fix('Hallo ?'));
    }

    public function testGreekLocale(): void
    {
        $fixer = new Fixer\SpaceBeforePunctuation('el_GR');

        $this->assertSame('Γεια!', $fixer->fix('Γεια !'));
        $this->assertSame('Γεια?', $fixer->fix('Γεια ?'));
    }

    public function testTurkishLocale(): void
    {
        $fixer = new Fixer\SpaceBeforePunctuation('tr_TR');

        $this->assertSame('Merhaba!', $fixer->fix('Merhaba !'));
        $this->assertSame('Merhaba?', $fixer->fix('Merhaba ?'));
    }

    public function testHungarianLocale(): void
    {
        $fixer = new Fixer\SpaceBeforePunctuation('hu_HU');

        $this->assertSame('Szia!', $fixer->fix('Szia !'));
        $this->assertSame('Szia?', $fixer->fix('Szia ?'));
    }

    public function testRomanianLocale(): void
    {
        $fixer = new Fixer\SpaceBeforePunctuation('ro_RO');

        $this->assertSame('Bună!', $fixer->fix('Bună !'));
        $this->assertSame('Bună?', $fixer->fix('Bună ?'));
    }

    public function testCatalanLocale(): void
    {
        $fixer = new Fixer\SpaceBeforePunctuation('ca_ES');

        // Catalan explicitly does NOT use French spacing rules
        $this->assertSame('Hola!', $fixer->fix('Hola !'));
        $this->assertSame('Hola?', $fixer->fix('Hola ?'));
    }
}

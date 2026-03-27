<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo\Tests\Fixer;

use JoliTypo\Exception\BadFixerConfigurationException;
use JoliTypo\Fixer;
use PHPUnit\Framework\TestCase;

class SmartQuotesTest extends TestCase
{
    public function testSimpleString(): void
    {
        $fixer = new Fixer\SmartQuotes('de');
        $this->assertInstanceOf('JoliTypo\Fixer\SmartQuotes', $fixer);

        $this->assertSame(Fixer::BDQUO . 'I am smart' . Fixer::LDQUO, $fixer->fix('"I am smart"'));

        $fixer->setOpening('«');
        $fixer->setClosing('»');

        $this->assertSame('«I am smart»', $fixer->fix('"I am smart"'));

        $fixer->setOpening('<');
        $fixer->setClosing('>');

        $this->assertSame('<I am smart>', $fixer->fix('"I am smart"'));
    }

    public function testBadConfig(): void
    {
        $this->expectException(BadFixerConfigurationException::class);

        $fixer = new Fixer\SmartQuotes('unknown');
        $fixer->fix('nope');
    }

    // =========================================================================
    // French style: « … » (with non-breaking spaces)
    // =========================================================================

    public function testFrenchQuoteStyle(): void
    {
        $fixer = new Fixer\SmartQuotes('fr');

        $this->assertSame(
            Fixer::LAQUO . Fixer::NO_BREAK_SPACE . 'Bonjour' . Fixer::NO_BREAK_SPACE . Fixer::RAQUO,
            $fixer->fix('"Bonjour"')
        );
    }

    public function testFrenchFranceQuoteStyle(): void
    {
        $fixer = new Fixer\SmartQuotes('fr_FR');

        $this->assertSame(
            Fixer::LAQUO . Fixer::NO_BREAK_SPACE . 'Bonjour' . Fixer::NO_BREAK_SPACE . Fixer::RAQUO,
            $fixer->fix('"Bonjour"')
        );
    }

    // =========================================================================
    // Guillemets without spaces: «…»
    // =========================================================================

    public function testRussianQuoteStyle(): void
    {
        $fixer = new Fixer\SmartQuotes('ru');

        $this->assertSame(Fixer::LAQUO . 'Привет' . Fixer::RAQUO, $fixer->fix('"Привет"'));
    }

    public function testSpanishQuoteStyle(): void
    {
        $fixer = new Fixer\SmartQuotes('es');

        $this->assertSame(Fixer::LAQUO . 'Hola' . Fixer::RAQUO, $fixer->fix('"Hola"'));
    }

    public function testItalianQuoteStyle(): void
    {
        $fixer = new Fixer\SmartQuotes('it');

        $this->assertSame(Fixer::LAQUO . 'Ciao' . Fixer::RAQUO, $fixer->fix('"Ciao"'));
    }

    public function testGreekQuoteStyle(): void
    {
        $fixer = new Fixer\SmartQuotes('el');

        $this->assertSame(Fixer::LAQUO . 'Γεια' . Fixer::RAQUO, $fixer->fix('"Γεια"'));
    }

    public function testPortugueseQuoteStyle(): void
    {
        $fixer = new Fixer\SmartQuotes('pt');

        $this->assertSame(Fixer::LAQUO . 'Olá' . Fixer::RAQUO, $fixer->fix('"Olá"'));
    }

    public function testUkrainianQuoteStyle(): void
    {
        $fixer = new Fixer\SmartQuotes('uk');

        $this->assertSame(Fixer::LAQUO . 'Привіт' . Fixer::RAQUO, $fixer->fix('"Привіт"'));
    }

    public function testNorwegianQuoteStyle(): void
    {
        $fixer = new Fixer\SmartQuotes('no');

        $this->assertSame(Fixer::LAQUO . 'Hei' . Fixer::RAQUO, $fixer->fix('"Hei"'));
    }

    public function testSwissGermanQuoteStyle(): void
    {
        $fixer = new Fixer\SmartQuotes('de_CH');

        // Swiss German uses guillemets without spaces
        $this->assertSame(Fixer::LAQUO . 'Hallo' . Fixer::RAQUO, $fixer->fix('"Hallo"'));
    }

    // =========================================================================
    // German style: „…" (low-high)
    // =========================================================================

    public function testGermanQuoteStyle(): void
    {
        $fixer = new Fixer\SmartQuotes('de');

        $this->assertSame(Fixer::BDQUO . 'Hallo' . Fixer::LDQUO, $fixer->fix('"Hallo"'));
    }

    public function testPolishQuoteStyle(): void
    {
        $fixer = new Fixer\SmartQuotes('pl');

        $this->assertSame(Fixer::BDQUO . 'Cześć' . Fixer::LDQUO, $fixer->fix('"Cześć"'));
    }

    public function testCzechQuoteStyle(): void
    {
        $fixer = new Fixer\SmartQuotes('cs');

        $this->assertSame(Fixer::BDQUO . 'Ahoj' . Fixer::LDQUO, $fixer->fix('"Ahoj"'));
    }

    public function testRomanianQuoteStyle(): void
    {
        $fixer = new Fixer\SmartQuotes('ro');

        $this->assertSame(Fixer::BDQUO . 'Bună' . Fixer::LDQUO, $fixer->fix('"Bună"'));
    }

    public function testHungarianQuoteStyle(): void
    {
        $fixer = new Fixer\SmartQuotes('hu');

        $this->assertSame(Fixer::BDQUO . 'Szia' . Fixer::LDQUO, $fixer->fix('"Szia"'));
    }

    public function testBulgarianQuoteStyle(): void
    {
        $fixer = new Fixer\SmartQuotes('bg');

        $this->assertSame(Fixer::BDQUO . 'Здравей' . Fixer::LDQUO, $fixer->fix('"Здравей"'));
    }

    // =========================================================================
    // English style: "…"
    // =========================================================================

    public function testEnglishQuoteStyle(): void
    {
        $fixer = new Fixer\SmartQuotes('en');

        $this->assertSame(Fixer::LDQUO . 'Hello' . Fixer::RDQUO, $fixer->fix('"Hello"'));
    }

    public function testDutchQuoteStyle(): void
    {
        $fixer = new Fixer\SmartQuotes('nl');

        $this->assertSame(Fixer::LDQUO . 'Hallo' . Fixer::RDQUO, $fixer->fix('"Hallo"'));
    }

    public function testTurkishQuoteStyle(): void
    {
        $fixer = new Fixer\SmartQuotes('tr');

        $this->assertSame(Fixer::LDQUO . 'Merhaba' . Fixer::RDQUO, $fixer->fix('"Merhaba"'));
    }

    public function testBrazilianPortugueseQuoteStyle(): void
    {
        $fixer = new Fixer\SmartQuotes('pt_BR');

        // Brazilian Portuguese uses English-style quotes
        $this->assertSame(Fixer::LDQUO . 'Olá' . Fixer::RDQUO, $fixer->fix('"Olá"'));
    }

    // =========================================================================
    // Finnish/Swedish style: "…" (same quote on both sides)
    // =========================================================================

    public function testFinnishQuoteStyle(): void
    {
        $fixer = new Fixer\SmartQuotes('fi');

        // Finnish uses the same closing quote on both sides
        $this->assertSame(Fixer::RDQUO . 'Hei' . Fixer::RDQUO, $fixer->fix('"Hei"'));
    }

    public function testSwedishQuoteStyle(): void
    {
        $fixer = new Fixer\SmartQuotes('sv');

        // Swedish uses the same closing quote on both sides
        $this->assertSame(Fixer::RDQUO . 'Hej' . Fixer::RDQUO, $fixer->fix('"Hej"'));
    }

    // =========================================================================
    // Locale can be changed
    // =========================================================================

    public function testLocaleCanBeChanged(): void
    {
        $fixer = new Fixer\SmartQuotes('en');
        $this->assertSame(Fixer::LDQUO . 'Hi' . Fixer::RDQUO, $fixer->fix('"Hi"'));

        $fixer->setLocale('de');
        $this->assertSame(Fixer::BDQUO . 'Hi' . Fixer::LDQUO, $fixer->fix('"Hi"'));

        $fixer->setLocale('fr');
        $this->assertSame(
            Fixer::LAQUO . Fixer::NO_BREAK_SPACE . 'Hi' . Fixer::NO_BREAK_SPACE . Fixer::RAQUO,
            $fixer->fix('"Hi"')
        );
    }
}

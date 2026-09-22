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

class SmartSingleQuotesTest extends TestCase
{
    public function testSimpleString(): void
    {
        $fixer = new Fixer\SmartSingleQuotes('en');
        $this->assertInstanceOf(Fixer\SmartSingleQuotes::class, $fixer);
        $this->assertSame(Fixer::LSQUO . 'I am smart' . Fixer::RSQUO, $fixer->fix("'I am smart'"));

        $fixer->setOpening('<');
        $fixer->setClosing('>');
        $this->assertSame('<I am smart>', $fixer->fix("'I am smart'"));

        $fixer->setOpeningSuffix(' ');
        $fixer->setClosingPrefix(' ');
        $this->assertSame('< I am smart >', $fixer->fix("'I am smart'"));
    }

    public function testBadConfig(): void
    {
        $this->expectException(BadFixerConfigurationException::class);
        $fixer = new Fixer\SmartSingleQuotes('unknown');
        $fixer->fix('nope');
    }

    public function testQuotesInSentences(): void
    {
        $fixer = new Fixer\SmartSingleQuotes('en');

        $this->assertSame('This ‘magic’ piece of code fixes quotes.', $fixer->fix("This 'magic' piece of code fixes quotes."));
        $this->assertSame('‘Good code is like a good joke.’', $fixer->fix("'Good code is like a good joke.'"));
        $this->assertSame('‘Good code is like a Bieber.’ - said no one, ever.', $fixer->fix("'Good code is like a Bieber.' - said no one, ever."));
        $this->assertSame('Some people are like ‘Batman’, others like ‘Superman’.', $fixer->fix("Some people are like 'Batman', others like 'Superman'."));
        $this->assertSame('A list of (‘words’ between ‘quotes’)!', $fixer->fix("A list of ('words' between 'quotes')!"));
        $this->assertSame('Is it ‘magic’? Yes: ‘magic’; really ‘magic’!', $fixer->fix("Is it 'magic'? Yes: 'magic'; really 'magic'!"));
        $this->assertSame("‘I'm here’, he said.", $fixer->fix("'I'm here', he said."));
        $this->assertSame("Multiple\nlines with ‘a quote\nspanning them’ work.", $fixer->fix("Multiple\nlines with 'a quote\nspanning them' work."));
    }

    public function testQuotesInsideDoubleQuotes(): void
    {
        $fixer = new Fixer\SmartSingleQuotes('en');

        $this->assertSame('"This ‘magic’ piece"', $fixer->fix("\"This 'magic' piece\""));
        $this->assertSame('“This ‘magic’ piece”', $fixer->fix("“This 'magic' piece”"));
        $this->assertSame('"‘Hi’ he said"', $fixer->fix("\"'Hi' he said\""));
        $this->assertSame('"He said ‘hi’"', $fixer->fix("\"He said 'hi'\""));
        $this->assertSame('“‘Hi’”', $fixer->fix("“'Hi'”"));

        $fixer->setLocale('de');
        $this->assertSame('„Er sagte ‚Hallo‘“', $fixer->fix("„Er sagte 'Hallo'“"));
        $this->assertSame('„‚Hallo‘, sagte er“', $fixer->fix("„'Hallo', sagte er“"));

        $fixer->setLocale('fr');
        $this->assertSame('«' . Fixer::NO_BREAK_SPACE . 'Il a dit “bonjour”' . Fixer::NO_BREAK_SPACE . '»', $fixer->fix('«' . Fixer::NO_BREAK_SPACE . "Il a dit 'bonjour'" . Fixer::NO_BREAK_SPACE . '»'));
    }

    public function testApostrophesAreLeftUntouched(): void
    {
        $fixer = new Fixer\SmartSingleQuotes('en');

        $this->assertSame("I'm SUPERMAN.", $fixer->fix("I'm SUPERMAN."));
        $this->assertSame("Qu'est ce que l'univers ?", $fixer->fix("Qu'est ce que l'univers ?"));
        $this->assertSame("Swag' me.", $fixer->fix("Swag' me."));
        $this->assertSame("She's 6' 10\".", $fixer->fix("She's 6' 10\"."));
        $this->assertSame("Back in the '90s.", $fixer->fix("Back in the '90s."));
        $this->assertSame("Here is a crying smiley: :'(", $fixer->fix("Here is a crying smiley: :'("));
        $this->assertSame("'An unclosed quote is left alone", $fixer->fix("'An unclosed quote is left alone"));
        $this->assertSame("The '90s were ‘great’.", $fixer->fix("The '90s were 'great'."));
    }

    public function testStylesByLocale(): void
    {
        $fixer = new Fixer\SmartSingleQuotes('en');
        $this->assertSame(Fixer::LSQUO . 'Hello' . Fixer::RSQUO, $fixer->fix("'Hello'"));

        $fixer->setLocale('en_US');
        $this->assertSame(Fixer::LSQUO . 'Hello' . Fixer::RSQUO, $fixer->fix("'Hello'"));

        $fixer->setLocale('pt_BR');
        $this->assertSame(Fixer::LSQUO . 'Olá' . Fixer::RSQUO, $fixer->fix("'Olá'"));

        $fixer->setLocale('de');
        $this->assertSame(Fixer::SBQUO . 'Hallo' . Fixer::LSQUO, $fixer->fix("'Hallo'"));

        $fixer->setLocale('de_DE');
        $this->assertSame(Fixer::SBQUO . 'Hallo' . Fixer::LSQUO, $fixer->fix("'Hallo'"));

        $fixer->setLocale('cs');
        $this->assertSame(Fixer::SBQUO . 'Ahoj' . Fixer::LSQUO, $fixer->fix("'Ahoj'"));

        $fixer->setLocale('de_CH');
        $this->assertSame(Fixer::LSAQUO . 'Hallo' . Fixer::RSAQUO, $fixer->fix("'Hallo'"));

        $fixer->setLocale('de-CH');
        $this->assertSame(Fixer::LSAQUO . 'Hallo' . Fixer::RSAQUO, $fixer->fix("'Hallo'"));

        $fixer->setLocale('fr');
        $this->assertSame(Fixer::LDQUO . 'Bonjour' . Fixer::RDQUO, $fixer->fix("'Bonjour'"));

        $fixer->setLocale('fr_FR');
        $this->assertSame(Fixer::LDQUO . 'Bonjour' . Fixer::RDQUO, $fixer->fix("'Bonjour'"));

        $fixer->setLocale('es');
        $this->assertSame(Fixer::LDQUO . 'Hola' . Fixer::RDQUO, $fixer->fix("'Hola'"));

        $fixer->setLocale('it');
        $this->assertSame(Fixer::LDQUO . 'Ciao' . Fixer::RDQUO, $fixer->fix("'Ciao'"));

        $fixer->setLocale('ru');
        $this->assertSame(Fixer::BDQUO . 'Привет' . Fixer::LDQUO, $fixer->fix("'Привет'"));

        $fixer->setLocale('pl');
        $this->assertSame(Fixer::LAQUO . 'Cześć' . Fixer::RAQUO, $fixer->fix("'Cześć'"));

        $fixer->setLocale('fi');
        $this->assertSame(Fixer::RSQUO . 'Hei' . Fixer::RSQUO, $fixer->fix("'Hei'"));

        $fixer->setLocale('sv');
        $this->assertSame(Fixer::RSQUO . 'Hej' . Fixer::RSQUO, $fixer->fix("'Hej'"));
    }

    public function testGermanReversedGuillemets(): void
    {
        // German books and newspapers often use »…« and ›…‹ (see issue #75)
        $fixer = new Fixer\SmartSingleQuotes('de_DE');
        $fixer->setOpening('›');
        $fixer->setClosing('‹');

        $this->assertSame('And this is an »example with another ›single quote‹ inside«.', $fixer->fix("And this is an »example with another 'single quote' inside«."));
    }

    public function testFullFixerNestedQuotes(): void
    {
        // See issue #69
        $fixer = new Fixer(['SmartQuotes', 'SmartSingleQuotes', 'CurlyQuote']);
        $fixer->setLocale('en_GB');

        $this->assertSame(
            '<p>“This ‘magic’ piece of code fixes dumb quotes and apostrophes, doesn’t it?”</p>',
            $this->decodeEntities($fixer->fix("<p>\"This 'magic' piece of code fixes dumb quotes and apostrophes, doesn't it?\"</p>"))
        );
    }

    public function testFullFixerGermanReversedGuillemets(): void
    {
        // See issue #75
        $doubleQuotes = new Fixer\SmartQuotes('de_DE');
        $doubleQuotes->setOpening('»');
        $doubleQuotes->setClosing('«');

        $singleQuotes = new Fixer\SmartSingleQuotes('de_DE');
        $singleQuotes->setOpening('›');
        $singleQuotes->setClosing('‹');

        $fixer = new Fixer(['Ellipsis', 'Dash', $doubleQuotes, $singleQuotes, 'CurlyQuote']);

        $this->assertSame(
            '<p>This is an »example«. And this is an »example with another ›single quote‹ inside«.</p>',
            $this->decodeEntities($fixer->fix("<p>This is an \"example\". And this is an \"example with another 'single quote' inside\".</p>"))
        );
    }

    public function testFullFixerGermanDefaults(): void
    {
        $fixer = new Fixer(['SmartQuotes', 'SmartSingleQuotes', 'CurlyQuote']);
        $fixer->setLocale('de_DE');

        $this->assertSame(
            '<p>Er sagte: „Das ist ‚toll‘, oder?“</p>',
            $this->decodeEntities($fixer->fix("<p>Er sagte: \"Das ist 'toll', oder?\"</p>"))
        );
    }

    public function testFullFixerFrench(): void
    {
        $fixer = new Fixer(['SmartQuotes', 'SmartSingleQuotes', 'SpaceBeforePunctuation', 'CurlyQuote']);
        $fixer->setLocale('fr_FR');

        $this->assertSame(
            '<p>Il a dit «' . Fixer::NO_BREAK_SPACE . 'c’est “super”' . Fixer::NO_BREAK_THIN_SPACE . '!' . Fixer::NO_BREAK_SPACE . '»</p>',
            $this->decodeEntities($fixer->fix("<p>Il a dit \"c'est 'super' !\"</p>"))
        );
    }

    public function testFullFixerSiblingNodes(): void
    {
        $fixer = new Fixer(['SmartQuotes', 'SmartSingleQuotes', 'CurlyQuote']);
        $fixer->setLocale('en_GB');

        $this->assertSame(
            '<p>He said ‘hello <b>world</b>’ and left. It’s “<em>done</em>”.</p>',
            $this->decodeEntities($fixer->fix("<p>He said 'hello <b>world</b>' and left. It's \"<em>done</em>\".</p>"))
        );
    }

    /**
     * libxml < 2.13 outputs named entities (&lsquo;), newer versions output the UTF-8 characters.
     */
    private function decodeEntities(string $html): string
    {
        return html_entity_decode($html, \ENT_QUOTES | \ENT_HTML5, 'UTF-8');
    }
}

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
    // Inch and second marks inside quotes (#32)
    // =========================================================================

    /**
     * @see https://github.com/jolicode/JoliTypo/issues/32
     */
    public function testInchAndSecondMarksInsideQuotes(): void
    {
        $fixer = new Fixer\SmartQuotes('en');

        // A double quote preceded by a digit is not a closing quote when a better candidate follows
        $this->assertSame('“The man was 5\'6" and 120 lbs.”', $fixer->fix('"The man was 5\'6" and 120 lbs."'));
        $this->assertSame('She said “the man was 5\'6" and 120 lbs” and left.', $fixer->fix('She said "the man was 5\'6" and 120 lbs" and left.'));
        $this->assertSame('He said “5\'6" is short”', $fixer->fix('He said "5\'6" is short"'));
        $this->assertSame('He said “hi” to the 27" monitor.', $fixer->fix('He said "hi" to the 27" monitor.'));
        $this->assertSame('The 27" monitor is “great”.', $fixer->fix('The 27" monitor is "great".'));

        // ... but it still closes a quote when nothing better follows
        $this->assertSame('She said “I am 5”.', $fixer->fix('She said "I am 5".'));
        $this->assertSame('“I am 5” and “you are 6”', $fixer->fix('"I am 5" and "you are 6"'));
        $this->assertSame('“I am 5” and 27" monitors', $fixer->fix('"I am 5" and 27" monitors'));

        // Inch marks outside quotes are left alone
        $this->assertSame('A 27" monitor and a 15.6" laptop.', $fixer->fix('A 27" monitor and a 15.6" laptop.'));
    }

    public function testInchAndSecondMarksInsideQuotesWithAffixes(): void
    {
        $fixer = new Fixer\SmartQuotes('fr');

        $this->assertSame('«' . Fixer::NO_BREAK_SPACE . 'The man was 5\'6" and 120 lbs.' . Fixer::NO_BREAK_SPACE . '»', $fixer->fix('"The man was 5\'6" and 120 lbs."'));
    }

    public function testInchAndSecondMarksInsideQuotesAcrossSiblingNodes(): void
    {
        $fixer = new Fixer(['SmartQuotes']);
        $fixer->setLocale('en');

        $this->assertSame('<p>“The man was <em>really</em> 5\'6" and 120 lbs.”</p>', $fixer->fix('<p>"The man was <em>really</em> 5\'6" and 120 lbs."</p>'));
        $this->assertSame('<p>“The man was<br>5\'6" and 120 lbs.”</p>', $fixer->fix('<p>"The man was<br>5\'6" and 120 lbs."</p>'));
        $this->assertSame('<p>He said “hi”<br>to the 27" monitor.</p>', $fixer->fix('<p>He said "hi"<br>to the 27" monitor.</p>'));
        $this->assertSame('<p>“I am <em>only</em> 5” and “you are 6”</p>', $fixer->fix('<p>"I am <em>only</em> 5" and "you are 6"</p>'));
        $this->assertSame('<p>“I am<br>5” tall</p>', $fixer->fix('<p>"I am<br>5" tall</p>'));
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

    // =========================================================================
    // Nested quotations: '…' inside "…" (#69, #75)
    // =========================================================================

    public function testNestedQuotesSimpleString(): void
    {
        $fixer = new Fixer\SmartQuotes('en');
        $this->assertSame(Fixer::LSQUO . 'I am smart' . Fixer::RSQUO, $fixer->fix("'I am smart'"));

        $fixer->setNestedOpening('<');
        $fixer->setNestedClosing('>');
        $this->assertSame('<I am smart>', $fixer->fix("'I am smart'"));
    }

    public function testNestedQuotesInSentences(): void
    {
        $fixer = new Fixer\SmartQuotes('en');

        $this->assertSame('This ‘magic’ piece of code fixes quotes.', $fixer->fix("This 'magic' piece of code fixes quotes."));
        $this->assertSame('‘Good code is like a good joke.’', $fixer->fix("'Good code is like a good joke.'"));
        $this->assertSame('‘Good code is like a Bieber.’ - said no one, ever.', $fixer->fix("'Good code is like a Bieber.' - said no one, ever."));
        $this->assertSame('Some people are like ‘Batman’, others like ‘Superman’.', $fixer->fix("Some people are like 'Batman', others like 'Superman'."));
        $this->assertSame('A list of (‘words’ between ‘quotes’)!', $fixer->fix("A list of ('words' between 'quotes')!"));
        $this->assertSame('Is it ‘magic’? Yes: ‘magic’; really ‘magic’!', $fixer->fix("Is it 'magic'? Yes: 'magic'; really 'magic'!"));
        $this->assertSame("‘I'm here’, he said.", $fixer->fix("'I'm here', he said."));
        $this->assertSame("Multiple\nlines with ‘a quote\nspanning them’ work.", $fixer->fix("Multiple\nlines with 'a quote\nspanning them' work."));
    }

    public function testNestedQuotesInsideDoubleQuotes(): void
    {
        $fixer = new Fixer\SmartQuotes('en');

        $this->assertSame('“This ‘magic’ piece”', $fixer->fix("\"This 'magic' piece\""));
        $this->assertSame('“‘Hi’ he said”', $fixer->fix("\"'Hi' he said\""));
        $this->assertSame('“He said ‘hi’”', $fixer->fix("\"He said 'hi'\""));
        $this->assertSame('“‘Hi’”', $fixer->fix("\"'Hi'\""));

        // Double quotes which are already smart
        $this->assertSame('“This ‘magic’ piece”', $fixer->fix("“This 'magic' piece”"));

        $fixer->setLocale('de');
        $this->assertSame('„Er sagte ‚Hallo‘“', $fixer->fix("\"Er sagte 'Hallo'\""));
        $this->assertSame('„‚Hallo‘, sagte er“', $fixer->fix("\"'Hallo', sagte er\""));

        $fixer->setLocale('fr');
        $this->assertSame('«' . Fixer::NO_BREAK_SPACE . 'Il a dit “bonjour”' . Fixer::NO_BREAK_SPACE . '»', $fixer->fix("\"Il a dit 'bonjour'\""));
    }

    public function testApostrophesAreLeftUntouched(): void
    {
        $fixer = new Fixer\SmartQuotes('en');

        $this->assertSame("I'm SUPERMAN.", $fixer->fix("I'm SUPERMAN."));
        $this->assertSame("Qu'est ce que l'univers ?", $fixer->fix("Qu'est ce que l'univers ?"));
        $this->assertSame("Swag' me.", $fixer->fix("Swag' me."));
        $this->assertSame("She's 6' 10\".", $fixer->fix("She's 6' 10\"."));
        $this->assertSame("Back in the '90s.", $fixer->fix("Back in the '90s."));
        $this->assertSame("Here is a crying smiley: :'(", $fixer->fix("Here is a crying smiley: :'("));
        $this->assertSame("'An unclosed quote is left alone", $fixer->fix("'An unclosed quote is left alone"));
        $this->assertSame("The '90s were ‘great’.", $fixer->fix("The '90s were 'great'."));
    }

    public function testNestedQuoteStylesByLocale(): void
    {
        $fixer = new Fixer\SmartQuotes('en');
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

    public function testNestedQuotesWithCustomMarksOnUnknownLocale(): void
    {
        // Without nested quotation marks, the single quotes are left as they are
        $fixer = new Fixer\SmartQuotes('unknown');
        $fixer->setOpening('«');
        $fixer->setClosing('»');

        $this->assertSame("«He said 'hi'»", $fixer->fix("\"He said 'hi'\""));

        $fixer->setNestedOpening('‹');
        $fixer->setNestedClosing('›');

        $this->assertSame('«He said ‹hi›»', $fixer->fix("\"He said 'hi'\""));
    }

    public function testGermanReversedGuillemets(): void
    {
        // German books and newspapers often use »…« and ›…‹ (see issue #75)
        $fixer = new Fixer\SmartQuotes('de_DE');
        $fixer->setOpening('»');
        $fixer->setClosing('«');
        $fixer->setNestedOpening('›');
        $fixer->setNestedClosing('‹');

        $this->assertSame(
            'This is an »example«. And this is an »example with another ›single quote‹ inside«.',
            $fixer->fix("This is an \"example\". And this is an \"example with another 'single quote' inside\".")
        );
    }

    public function testFullFixerNestedQuotes(): void
    {
        // See issue #69
        $fixer = new Fixer(['SmartQuotes', 'CurlyQuote']);
        $fixer->setLocale('en_GB');

        $this->assertSame(
            '<p>“This ‘magic’ piece of code fixes dumb quotes and apostrophes, doesn’t it?”</p>',
            $fixer->fix("<p>\"This 'magic' piece of code fixes dumb quotes and apostrophes, doesn't it?\"</p>")
        );
    }

    public function testFullFixerGermanReversedGuillemets(): void
    {
        // See issue #75
        $smartQuotes = new Fixer\SmartQuotes('de_DE');
        $smartQuotes->setOpening('»');
        $smartQuotes->setClosing('«');
        $smartQuotes->setNestedOpening('›');
        $smartQuotes->setNestedClosing('‹');

        $fixer = new Fixer(['Ellipsis', 'Dash', $smartQuotes, 'CurlyQuote']);

        $this->assertSame(
            '<p>This is an »example«. And this is an »example with another ›single quote‹ inside«.</p>',
            $fixer->fix("<p>This is an \"example\". And this is an \"example with another 'single quote' inside\".</p>")
        );
    }

    public function testFullFixerGermanDefaults(): void
    {
        $fixer = new Fixer(['SmartQuotes', 'CurlyQuote']);
        $fixer->setLocale('de_DE');

        $this->assertSame(
            '<p>Er sagte: „Das ist ‚toll‘, oder?“</p>',
            $fixer->fix("<p>Er sagte: \"Das ist 'toll', oder?\"</p>")
        );
    }

    public function testFullFixerFrenchNestedQuotes(): void
    {
        $fixer = new Fixer(['SmartQuotes', 'SpaceBeforePunctuation', 'CurlyQuote']);
        $fixer->setLocale('fr_FR');

        $this->assertSame(
            '<p>Il a dit «&nbsp;c’est “super”' . Fixer::NO_BREAK_THIN_SPACE . '!&nbsp;»</p>',
            $fixer->fix("<p>Il a dit \"c'est 'super' !\"</p>")
        );
    }

    public function testFullFixerNestedQuotesAcrossSiblingNodes(): void
    {
        $fixer = new Fixer(['SmartQuotes', 'CurlyQuote']);
        $fixer->setLocale('en_GB');

        $this->assertSame(
            '<p>He said ‘hello <b>world</b>’ and left. It’s “<em>done</em>”.</p>',
            $fixer->fix("<p>He said 'hello <b>world</b>' and left. It's \"<em>done</em>\".</p>")
        );
    }
}

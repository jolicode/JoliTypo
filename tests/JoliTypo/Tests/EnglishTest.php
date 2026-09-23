<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo\Tests;

use JoliTypo\Fixer;
use PHPUnit\Framework\TestCase;

class EnglishTest extends TestCase
{
    public const TOFIX = <<<'TOFIX'
        <!-- From https://en.wikipedia.org/wiki/Gif#Pronunciation -->
        <h3>Pronunciation</h3>

        <p>A humorous image announcing the launch of a White House Tumblr suggests pronouncing GIF with a hard "G".</p>
        <p>The creators of the format pronounced GIF as "Jif" with a soft "G" /ˈdʒɪf/ as in "gin".</p>
        <p>An alternative pronunciation with a hard "G" /ˈɡɪf/ as in "graphics", reflecting the expanded acronym, is in widespread usage.</p>
        <p>Both pronunciations are acknowledged by the [...] Merriam-Webster's Collegiate Dictionary.</p>

        <p>We also have "<span>HTML in quote</span>" to fix...</p>
        TOFIX;

    /**
     * The expected outputs are UTF-8: \u{AD} is a soft hyphen (&shy;) and \u{202F} a narrow no-break space (&#8239;),
     * both invisible. The HTML5 serializer only keeps &nbsp;, &amp;, &lt; and &gt; as entities.
     */
    public const FIXED = <<<FIXED
        <!-- From https://en.wikipedia.org/wiki/Gif#Pronunciation -->
        <h3>Pronun\u{AD}ci\u{AD}ation</h3>

        <p>A humor\u{AD}ous image announ\u{AD}cing the launch of a White House Tumblr suggests pronoun\u{AD}cing GIF with a hard “G”.</p>
        <p>The creat\u{AD}ors of the format pronounced GIF as “Jif” with a soft “G” /ˈdʒɪf/ as in “gin”.</p>
        <p>An altern\u{AD}at\u{AD}ive pronun\u{AD}ci\u{AD}ation with a hard “G” /ˈɡɪf/ as in “graph\u{AD}ics”, reflect\u{AD}ing the expan\u{AD}ded acronym, is in wide\u{AD}spread usage.</p>
        <p>Both pronun\u{AD}ci\u{AD}ations are acknow\u{AD}ledged by the […] Merriam-Webster’s Collegi\u{AD}ate Diction\u{AD}ary.</p>

        <p>We also have “<span>HTML in quote</span>” to fix…</p>
        FIXED;
    private $en_fixers = ['Unit', 'Ellipsis', 'Dimension', 'Dash', 'SmartQuotes', 'CurlyQuote', 'Hyphen', 'Trademark'];

    public function testFixFullText(): void
    {
        $fixer = new Fixer($this->en_fixers);
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $this->assertSame(self::FIXED, $fixer->fix(self::TOFIX));
    }

    /**
     * @see https://github.com/jolicode/JoliTypo/issues/57
     */
    public function testFixIsIdempotent(): void
    {
        $fixer = new Fixer($this->en_fixers);

        $fixed = $fixer->fix(self::TOFIX);

        $this->assertSame($fixed, $fixer->fix($fixed));
    }

    public function testReadMeExemple(): void
    {
        $before = <<<'HTML'
            <p>"Tell me Mr. Anderson... what good is a phone call... if you're unable to speak?" -- Agent Smith, <em>Matrix</em>.</p>
            HTML;

        $after = <<<HTML
            <p>“Tell me Mr. Ander\u{AD}son… what good is a phone call… if you’re unable to speak?”—Agent Smith, <em>Matrix</em>.</p>
            HTML;

        $fixer = new Fixer($this->en_fixers);
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);
        $this->assertSame($after, $fixer->fix($before));
    }

    public function testDoubleQuoteMess(): void
    {
        $fixed = <<<'HTML'
            <p>I’m learning “<a href="http://composer.json.jolicode.com">composer.json</a>” as it’s better than a “.docx”</p>
            HTML;

        $to_fix = <<<'HTML'
            <p>I'm learning "<a href="http://composer.json.jolicode.com">composer.json</a>" as it's better than a ".docx"</p>
            HTML;
        $fixer = new Fixer($this->en_fixers);
        $fixer->setLocale('en');
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);
        $this->assertSame($fixed, $fixer->fix($to_fix));
    }

    public function testHtmlHeart(): void
    {
        $fixed = <<<'HTML'
            <p>We &lt;3&nbsp;web.</p>
            HTML;

        $to_fix = <<<'HTML'
            <p>We &lt;3 web.</p>
            HTML;
        $fixer = new Fixer($this->en_fixers);
        $fixer->setLocale('en');
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);
        $this->assertSame($fixed, $fixer->fix($to_fix));
    }
}

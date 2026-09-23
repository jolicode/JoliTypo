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

class FrenchTest extends TestCase
{
    public const TOFIX = <<<'TOFIX'
        <p>Ceci est à remplacer par une fâble :p</p>

        <pre>Oh, du "code" encodé, mais pas double encodé: &amp;!</pre>

        <p>Le mec a fini sa course en 2'33" contre 2'44" pour le second !</p>

        <p>Je suis "très
        content" de t'avoir <a href="http://coucou">invité</a> !</p>

        <pre><code>
          &lt;a href=""&gt;
          pre
        </code></pre>

        <p>Ceci &eacute;té un "CHOQUE"&nbsp;! Son salon fait 4x4 m, ce qui est plutôt petit.</p>

        <p>Les trés long mots sont tronqués, comme "renseignements" par exemple.</p>

        <p>Du HTML dans une citation : "Je suis <strong>fan</strong> de JoliTypo" pose problème.</p>

        <p>Une autre exemple : "<strong>Citation forte !</strong>".</p>
        TOFIX;

    /**
     * The expected outputs are UTF-8: \u{AD} is a soft hyphen (&shy;) and \u{202F} a narrow no-break space (&#8239;),
     * both invisible. The HTML5 serializer only keeps &nbsp;, &amp;, &lt; and &gt; as entities.
     */
    public const FIXED = <<<FIXED
        <p>Ceci est à rempla\u{AD}cer par une fâble&nbsp;:p</p>

        <pre>Oh, du "code" encodé, mais pas double encodé: &amp;!</pre>

        <p>Le mec a fini sa course en 2'33" contre 2'44" pour le second\u{202F}!</p>

        <p>Je suis «&nbsp;très
        content&nbsp;» de t’avoir <a href="http://coucou">invité</a>\u{202F}!</p>

        <pre><code>
          &lt;a href=""&gt;
          pre
        </code></pre>

        <p>Ceci été un «&nbsp;CHOQUE&nbsp;»\u{202F}! Son salon fait 4×4&nbsp;m, ce qui est plutôt petit.</p>

        <p>Les trés long mots sont tron\u{AD}qués, comme «&nbsp;rensei\u{AD}gne\u{AD}ments&nbsp;» par exemple.</p>

        <p>Du HTML dans une cita\u{AD}tion&nbsp;: «&nbsp;Je suis <strong>fan</strong> de Joli\u{AD}Typo&nbsp;» pose problème.</p>

        <p>Une autre exemple&nbsp;: «&nbsp;<strong>Cita\u{AD}tion forte\u{202F}!</strong>&nbsp;».</p>
        FIXED;
    private $fr_fixers = ['Unit', 'Ellipsis', 'Dimension', 'Dash', 'SmartQuotes', 'FrenchNoBreakSpace', 'CurlyQuote', 'Hyphen', 'Trademark'];

    public function testFixFullText(): void
    {
        $fixer = new Fixer($this->fr_fixers);
        $fixer->setLocale('fr_FR');
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $this->assertSame(self::FIXED, $fixer->fix(self::TOFIX));
    }

    public function testFixFullTextShort(): void
    {
        $fixer = new Fixer($this->fr_fixers);
        $fixer->setLocale('fr');
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $this->assertSame(self::FIXED, $fixer->fix(self::TOFIX));
    }

    /**
     * @see https://github.com/jolicode/JoliTypo/issues/57
     */
    public function testFixIsIdempotent(): void
    {
        $fixer = new Fixer($this->fr_fixers);
        $fixer->setLocale('fr_FR');

        $fixed = $fixer->fix(self::TOFIX);

        $this->assertSame($fixed, $fixer->fix($fixed));
    }

    public function testDoubleQuoteMess(): void
    {
        $fixer = new Fixer($this->fr_fixers);
        $fixer->setLocale('fr');
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $fixed = <<<HTML
            <p>A la sauce «&nbsp;<a href="http://composer.json.jolicode.com">compo\u{AD}ser.json</a>&nbsp;»
             atti\u{AD}rera forcé\u{AD}ment plus notre atten\u{AD}tion qu’une lettre de moti\u{AD}va\u{AD}tion de 4&nbsp;pages en «&nbsp;.docx&nbsp;»</p>
            HTML;

        $to_fix = <<<'HTML'
            <p>A la sauce "<a href="http://composer.json.jolicode.com">composer.json</a>"
             attirera forcément plus notre attention qu’une lettre de motivation de 4 pages en ".docx"</p>
            HTML;

        $this->assertSame($fixed, $fixer->fix($to_fix));
    }

    public function testEncodingMess(): void
    {
        $fixer = new Fixer($this->fr_fixers);
        $fixer->setLocale('fr');
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $fixed = <<<HTML
            Ça s’ar\u{AD}rête là\u{202F}!
            HTML;

        $to_fix = <<<'HTML'
            Ça s'arrête là !
            HTML;

        $this->assertSame($fixed, $fixer->fix($to_fix));
    }

    /**
     * @see https://github.com/jolicode/JoliTypo/issues/16
     */
    public function testNoBreakingSpaceInsideGoodQuotes(): void
    {
        $fixer = new Fixer($this->fr_fixers);

        $fixer->setLocale('fr');
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $fixed = <<<'HTML'
            «&nbsp;test&nbsp;» et «&nbsp;test&nbsp;» sont dans un bateau.
            HTML;

        $to_fix = <<<'HTML'
            « test » et «test» sont dans un bateau.
            HTML;

        $this->assertSame($fixed, $fixer->fix($to_fix));

        $to_fix = <<<'HTML'
            &laquo; test &raquo; et &laquo;test&raquo; sont dans un bateau.
            HTML;

        $this->assertSame($fixed, $fixer->fix($to_fix));
    }

    /**
     * @see https://github.com/jolicode/JoliTypo/issues/15
     */
    public function testNumericDoesNotBreakOtherFixers(): void
    {
        $fixer = new Fixer($this->fr_fixers);

        $fixer->setLocale('fr');
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $fixed = <<<'HTML'
            2&nbsp;×&nbsp;5&nbsp;doit être corrigé, et 2&nbsp;h aussi.
            HTML;

        $to_fix = <<<'HTML'
            2 x 5 doit être corrigé, et 2 h aussi.
            HTML;

        $this->assertSame($fixed, $fixer->fix($to_fix));
    }

    /**
     * @see https://github.com/jolicode/JoliTypo/issues/35
     */
    public function testWeirdHyphen(): void
    {
        $fixer = new Fixer($this->fr_fixers);

        $fixer->setLocale('fr');
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $to_fix = <<<'HTML'
            <p><a href="http://foobar.dev/storage/image-1493026187479.gif" target="_self"><img src="http://foobar.dev/storage/image-1493026187479.gif" alt="file"></a></p>
            HTML;

        $this->assertStringNotContainsString('&shy;', $fixer->fix($to_fix));
        $this->assertStringNotContainsString(Fixer::SHY, $fixer->fix($to_fix));
    }

    /**
     * @see https://github.com/jolicode/JoliTypo/issues/93
     */
    public function testEncodingOfLigature(): void
    {
        $fixer = new Fixer($this->fr_fixers);

        $fixer->setLocale('fr');
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $to_fix = <<<'HTML'
            <p>des œuvres d'art.</p>
            HTML;

        $this->assertSame('<p>des œuvres d’art.</p>', $fixer->fix($to_fix));
    }
}

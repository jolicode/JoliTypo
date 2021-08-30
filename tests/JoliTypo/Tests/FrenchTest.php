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
    private $fr_fixers = ['Unit', 'Ellipsis', 'Dimension', 'Dash', 'SmartQuotes', 'FrenchNoBreakSpace', 'CurlyQuote', 'Hyphen', 'Trademark'];

    const TOFIX = <<<TOFIX
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

    const FIXED = <<<FIXED
<p>Ceci est &agrave; rempla&shy;cer par une f&acirc;ble&nbsp;:p</p>

<pre>Oh, du "code" encod&eacute;, mais pas double encod&eacute;: &amp;!</pre>

<p>Le mec a fini sa course en 2'33" contre 2'44" pour le second&#8239;!</p>

<p>Je suis &laquo;&nbsp;tr&egrave;s
content&nbsp;&raquo; de t&rsquo;avoir <a href="http://coucou">invit&eacute;</a>&#8239;!</p>

<pre><code>
  &lt;a href=""&gt;
  pre
</code></pre>

<p>Ceci &eacute;t&eacute; un &laquo;&nbsp;CHOQUE&nbsp;&raquo;&#8239;! Son salon fait 4&times;4&nbsp;m, ce qui est plut&ocirc;t petit.</p>

<p>Les tr&eacute;s long mots sont tronqu&eacute;s, comme &laquo;&nbsp;rensei&shy;gne&shy;ments&nbsp;&raquo; par exemple.</p>

<p>Du HTML dans une cita&shy;tion&nbsp;: &laquo;&nbsp;Je suis <strong>fan</strong> de Joli&shy;Typo&nbsp;&raquo; pose probl&egrave;me.</p>

<p>Une autre exemple&nbsp;: &laquo;&nbsp;<strong>Cita&shy;tion forte&#8239;!</strong>&nbsp;&raquo;.</p>
FIXED;

    public function testFixFullText()
    {
        $fixer = new Fixer($this->fr_fixers);
        $fixer->setLocale('fr_FR');
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $this->assertEquals(self::FIXED, $fixer->fix(self::TOFIX));
    }

    public function testFixFullTextShort()
    {
        $fixer = new Fixer($this->fr_fixers);
        $fixer->setLocale('fr');
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $this->assertEquals(self::FIXED, $fixer->fix(self::TOFIX));
    }

    public function testDoubleQuoteMess()
    {
        $fixer = new Fixer($this->fr_fixers);
        $fixer->setLocale('fr');
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $fixed = <<<HTML
<p>A la sauce &laquo;&nbsp;<a href="http://composer.json.jolicode.com">compo&shy;ser.json</a>&nbsp;&raquo;
 atti&shy;rera forc&eacute;&shy;ment plus notre atten&shy;tion qu&rsquo;une lettre de moti&shy;va&shy;tion de 4&nbsp;pages en &laquo;&nbsp;.docx&nbsp;&raquo;</p>
HTML;

        $to_fix = <<<HTML
<p>A la sauce "<a href="http://composer.json.jolicode.com">composer.json</a>"
 attirera forcément plus notre attention qu’une lettre de motivation de 4 pages en ".docx"</p>
HTML;

        $this->assertEquals($fixed, $fixer->fix($to_fix));
    }

    public function testEncodingMess()
    {
        $fixer = new Fixer($this->fr_fixers);
        $fixer->setLocale('fr');
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $fixed = <<<HTML
&Ccedil;a s&rsquo;ar&shy;r&ecirc;te l&agrave;&#8239;!
HTML;

        $to_fix = <<<HTML
Ça s'arrête là !
HTML;

        $this->assertEquals($fixed, $fixer->fix($to_fix));
    }

    /**
     * @see https://github.com/jolicode/JoliTypo/issues/16
     */
    public function testNoBreakingSpaceInsideGoodQuotes()
    {
        $fixer = new Fixer($this->fr_fixers);

        $fixer->setLocale('fr');
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $fixed = <<<HTML
&laquo;&nbsp;test&nbsp;&raquo; et &laquo;&nbsp;test&nbsp;&raquo; sont dans un bateau.
HTML;

        $to_fix = <<<HTML
« test » et «test» sont dans un bateau.
HTML;

        $this->assertEquals($fixed, $fixer->fix($to_fix));

        $to_fix = <<<HTML
&laquo; test &raquo; et &laquo;test&raquo; sont dans un bateau.
HTML;

        $this->assertEquals($fixed, $fixer->fix($to_fix));
    }

    /**
     * @see https://github.com/jolicode/JoliTypo/issues/15
     */
    public function testNumericDoesNotBreakOtherFixers()
    {
        $fixer = new Fixer($this->fr_fixers);

        $fixer->setLocale('fr');
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $fixed = <<<HTML
2&nbsp;&times;&nbsp;5&nbsp;doit &ecirc;tre corrig&eacute;, et 2&nbsp;h aussi.
HTML;

        $to_fix = <<<HTML
2 x 5 doit être corrigé, et 2 h aussi.
HTML;

        $this->assertEquals($fixed, $fixer->fix($to_fix));
    }

    /**
     * @see https://github.com/jolicode/JoliTypo/issues/35
     */
    public function testWeirdHyphen()
    {
        $fixer = new Fixer($this->fr_fixers);

        $fixer->setLocale('fr');
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $to_fix = <<<HTML
<p><a href="http://foobar.dev/storage/image-1493026187479.gif" target="_self"><img src="http://foobar.dev/storage/image-1493026187479.gif" alt="file"></a></p>
HTML;

        $this->assertStringNotContainsString('&shy;', $fixer->fix($to_fix));
        $this->assertStringNotContainsString(Fixer::SHY, $fixer->fix($to_fix));
    }
}

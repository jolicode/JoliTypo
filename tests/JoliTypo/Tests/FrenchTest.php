<?php
namespace JoliTypo\Tests;

use JoliTypo\Fixer;

class FrenchTest extends \PHPUnit_Framework_TestCase
{
    private $fr_fixers = array('Ellipsis', 'Dimension', 'Dash', 'FrenchQuotes', 'FrenchNoBreakSpace', 'CurlyQuote', 'Hyphen', 'Trademark');

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

<p>Ceci &eacute;té un "CHOQUE"&nbsp;! Son salon fait 4x4m, ce qui est plutôt petit.</p>

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

<p>Ceci &eacute;t&eacute; un &laquo;&nbsp;CHOQUE&nbsp;&raquo;&#8239;! Son salon fait 4&times;4m, ce qui est plut&ocirc;t petit.</p>

<p>Les tr&eacute;s long mots sont tronqu&eacute;s, comme &laquo;&nbsp;rensei&shy;gne&shy;ments&nbsp;&raquo; par exemple.</p>

<p>Du HTML dans une cita&shy;tion&nbsp;: &laquo;&nbsp;Je suis <strong>fan</strong> de JoliTypo&nbsp;&raquo; pose probl&egrave;me.</p>

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
 atti&shy;rera forc&eacute;&shy;ment plus notre atten&shy;tion qu&rsquo;une lettre de moti&shy;va&shy;tion de 4 pages en &laquo;&nbsp;.docx&nbsp;&raquo;</p>
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
}

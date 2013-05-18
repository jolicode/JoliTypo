<?php
namespace JoliTypo\Tests;

use JoliTypo\Fixer;

class FrenchTest extends \PHPUnit_Framework_TestCase
{
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

<p>Les tr&eacute;s long mots sont tron&shy;qu&eacute;s, comme &laquo;&nbsp;rensei&shy;gne&shy;ments&nbsp;&raquo; par exemple.</p>
FIXED;

    public function testFixFullText()
    {
        $fixer = new Fixer('fr_FR');
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $this->assertEquals(self::FIXED, $fixer->fix(self::TOFIX));
    }
}

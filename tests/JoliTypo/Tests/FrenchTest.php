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
TOFIX;

    const FIXED = <<<FIXED
<p>Ceci est &agrave; remplacer par une f&acirc;ble&nbsp;:p</p>

<pre>Oh, du "code" encod&eacute;, mais pas double encod&eacute;: &amp;!</pre>

<p>Le mec a fini sa course en 2'33" contre 2'44" pour le second&nbsp;!</p>

<p>Je suis &laquo;&#8239;tr&egrave;s
content&#8239;&raquo; de t&rsquo;avoir <a href="http://coucou">invit&eacute;</a>&nbsp;!</p>

<pre><code>
  &lt;a href=""&gt;
  pre
</code></pre>

<p>Ceci &eacute;t&eacute; un &laquo;&#8239;CHOQUE&#8239;&raquo;&nbsp;! Son salon fait 4&times;4m, ce qui est plut&ocirc;t petit.</p>
FIXED;

    public function testRegisterProvider()
    {
        $fixer = new Fixer('fr_FR');
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $this->assertEquals(self::FIXED, $fixer->fix(self::TOFIX));
    }
}

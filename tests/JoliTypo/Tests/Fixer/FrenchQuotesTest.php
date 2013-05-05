<?php
namespace JoliTypo\Tests\Fixer;

class FrenchQuotesTest extends \PHPUnit_Framework_TestCase
{
    const TOFIX = <<<TOFIX
<p>Ceci est à remplacer par une fâble :p</p>

<pre>Oh, du "code"!</pre>

<p>Le mec a fini sa course en 2'33" contre 2'44" pour le second !</p>

<p>Je suis "très
content" de t'avoir <a href="http://coucou">invité</a> !</p>

<pre><code>
  &lt;a href=""&gt;
  pre
</code></pre>

<p>Ceci &eacute;té un "CHOQUE"&nbsp;!</p>
TOFIX;

    const FIXED = <<<FIXED
<p>Ceci est &agrave; remplacer par une f&acirc;ble&nbsp;:p</p>

<pre>Oh, du "code"!</pre>

<p>Le mec a fini sa course en 2'33" contre 2'44" pour le second&nbsp;!</p>

<p>Je suis &laquo;&#8239;tr&egrave;s
content&#8239;&raquo; de t'avoir <a href="http://coucou">invit&eacute;</a>&nbsp;!</p>

<pre><code>
  &lt;a href=""&gt;
  pre
</code></pre>

<p>Ceci &eacute;t&eacute; un &laquo;&#8239;CHOQUE&#8239;&raquo;&nbsp;!</p>
FIXED;

    public function testRegisterProvider()
    {
        $fixer = new \JoliTypo\Fixer();
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $this->assertEquals(self::FIXED, $fixer->fix(self::TOFIX));
    }
}

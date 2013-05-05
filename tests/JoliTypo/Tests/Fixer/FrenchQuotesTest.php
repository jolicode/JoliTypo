<?php
namespace JoliTypo\Tests\Fixer;

class FrenchQuotesTest extends \PHPUnit_Framework_TestCase
{
    const TOFIX = <<<TOFIX
<p>Ceci est à remplacer par une fâble :p</p>

<pre>Oh, du "code"!</pre>

<p>Je suis "très content" de t'avoir <a href="http://coucou">invité</a> !</p>

<pre><code>
  &lt;a href=&quot;&quot;&gt;
  pre
</code></pre>
TOFIX;

    const FIXED = <<<FIXED
<p>Ceci est à remplacer par une fâble :p</p>

<pre>Oh, du "code"!</pre>

<p>Je suis &#171;&#8239;très content&#8239;&#187; de t'avoir <a href="http://coucou">invité</a> !</p>

<pre><code>
  &lt;a href=&quot;&quot;&gt;
  pre
</code></pre>
FIXED;

    public function testRegisterProvider()
    {
        $fixer = new \JoliTypo\Fixer();
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $this->assertEquals(self::FIXED, $fixer->fix(self::TOFIX));
    }
}

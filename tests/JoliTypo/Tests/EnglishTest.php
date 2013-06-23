<?php
namespace JoliTypo\Tests;

use JoliTypo\Fixer;

class EnglishTest extends \PHPUnit_Framework_TestCase
{
    const TOFIX = <<<TOFIX
<!-- From https://en.wikipedia.org/wiki/Gif#Pronunciation -->
<h3>Pronunciation</h3>

<p>A humorous image announcing the launch of a White House Tumblr suggests pronouncing GIF with a hard "G".</p>
<p>The creators of the format pronounced GIF as "Jif" with a soft "G" /ˈdʒɪf/ as in "gin".</p>
<p>An alternative pronunciation with a hard "G" /ˈɡɪf/ as in "graphics", reflecting the expanded acronym, is in widespread usage.</p>
<p>Both pronunciations are acknowledged by the [...] Merriam-Webster's Collegiate Dictionary.</p>

<p>We also have "<span>HTML in quote</span>" to fix...</p>
TOFIX;

    const FIXED = <<<FIXED
<!-- From https://en.wikipedia.org/wiki/Gif#Pronunciation -->
<h3>Pronunciation</h3>

<p>A humorous image announcing the launch of a White House Tumblr suggests pronouncing GIF with a hard "G".</p>
<p>The creators of the format pronounced GIF as "Jif" with a soft "G" /ˈdʒɪf/ as in "gin".</p>
<p>An alternative pronunciation with a hard "G" /ˈɡɪf/ as in "graphics", reflecting the expanded acronym, is in widespread usage.</p>
<p>Both pronunciations are acknowledged by the [...] Merriam-Webster's Collegiate Dictionary.</p>

<p>We also have "<span>HTML in quote</span>" to fix...</p>
FIXED;

    public function testFixFullText()
    {
        $fixer = new Fixer('en_GB');
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $this->assertEquals(self::FIXED, $fixer->fix(self::TOFIX));
    }
}

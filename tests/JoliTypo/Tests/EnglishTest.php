<?php
namespace JoliTypo\Tests;

use JoliTypo\Fixer;

class EnglishTest extends \PHPUnit_Framework_TestCase
{
    private $en_fixers = array('Ellipsis', 'Dimension', 'Dash', 'EnglishQuotes', 'CurlyQuote', 'Hyphen');

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
<h3>Pronun&shy;ci&shy;ation</h3>

<p>A humor&shy;ous image announ&shy;cing the launch of a White House Tumblr suggests pronoun&shy;cing GIF with a hard &ldquo;G&rdquo;.</p>
<p>The creat&shy;ors of the format pronounced GIF as &ldquo;Jif&rdquo; with a soft &ldquo;G&rdquo; /&#712;d&#658;&#618;f/ as in &ldquo;gin&rdquo;.</p>
<p>An altern&shy;at&shy;ive pronun&shy;ci&shy;ation with a hard &ldquo;G&rdquo; /&#712;&#609;&#618;f/ as in &ldquo;graph&shy;ics&rdquo;, reflect&shy;ing the expan&shy;ded acronym, is in wide&shy;spread usage.</p>
<p>Both pronun&shy;ci&shy;ations are acknow&shy;ledged by the [&hellip;] Merriam-Webster&rsquo;s Collegi&shy;ate Diction&shy;ary.</p>

<p>We also have &ldquo;<span>HTML in quote</span>&rdquo; to fix&hellip;</p>
FIXED;

    public function testFixFullText()
    {
        $fixer = new Fixer($this->en_fixers);
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $this->assertEquals(self::FIXED, $fixer->fix(self::TOFIX));
    }
}

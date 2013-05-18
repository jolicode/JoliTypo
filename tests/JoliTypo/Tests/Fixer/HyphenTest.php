<?php
namespace JoliTypo\Tests\Fixer;

use JoliTypo\Fixer;

class HyphenTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleString()
    {
        $fixer = new Fixer\Hyphen('fr');
        $this->assertInstanceOf('JoliTypo\Fixer\Hyphen', $fixer);

        $this->assertEquals("Test", $fixer->fix("Test"));
        $this->assertEquals("Cordia".Fixer::SHY."le".Fixer::SHY."ment", $fixer->fix("Cordialement"));
        $this->assertEquals("Cordia".Fixer::SHY."le".Fixer::SHY."ment".Fixer::NO_BREAK_THIN_SPACE."!", $fixer->fix("Cordialement".Fixer::NO_BREAK_THIN_SPACE."!"));
    }

    public function testLocaleFallback()
    {
        $fixer = new Fixer\Hyphen('fr_BE');
        $this->assertInstanceOf('JoliTypo\Fixer\Hyphen', $fixer);

        $this->assertEquals("Test", $fixer->fix("Test"));
        $this->assertEquals("Cordia".Fixer::SHY."le".Fixer::SHY."ment", $fixer->fix("Cordialement"));
    }

    public function testNonExistingLocale()
    {
        $fixer = new Fixer\Hyphen('toto');

        $this->assertEquals("Test", $fixer->fix("Test"));
        $this->assertEquals("Cordialement", $fixer->fix("Cordialement"));
    }
}

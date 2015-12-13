<?php
namespace JoliTypo\Tests\Fixer;

use JoliTypo\Fixer;

class NumericTest extends \PHPUnit_Framework_TestCase
{
    public function testNumericUnits()
    {
        $fixer = new Fixer\Numeric();
        $this->assertInstanceOf('JoliTypo\Fixer\Numeric', $fixer);

        $this->assertEquals("Test", $fixer->fix("Test"));
        $this->assertEquals("1".Fixer::NO_BREAK_SPACE."h", $fixer->fix("1 h"));
        $this->assertEquals("2".Fixer::NO_BREAK_SPACE."฿", $fixer->fix("2 ฿"));
        $this->assertEquals("3".Fixer::NO_BREAK_SPACE."things", $fixer->fix("3 things"));
        $this->assertEquals("4,5".Fixer::NO_BREAK_SPACE."km", $fixer->fix("4,5 km"));
        $this->assertEquals("5.1".Fixer::NO_BREAK_SPACE."deg", $fixer->fix("5.1 deg"));
        $this->assertEquals("6".Fixer::NO_BREAK_SPACE."ºC", $fixer->fix("6 ºC"));
        $this->assertEquals("7".Fixer::NO_BREAK_SPACE."$", $fixer->fix("7 $"));
        $this->assertEquals("8º".Fixer::NO_BREAK_SPACE."18′ 30″", $fixer->fix("8º 18′ 30″"));
        $this->assertEquals("9".Fixer::NO_BREAK_SPACE."hours", $fixer->fix("9 hours"));
        $this->assertEquals("10e position", $fixer->fix("10e position")); // We can't fix this reliably
        $this->assertEquals("nº".Fixer::NO_BREAK_SPACE."11", $fixer->fix("nº 11"));
        $this->assertEquals("12".Fixer::NO_BREAK_SPACE."%", $fixer->fix("12 %"));
    }
}

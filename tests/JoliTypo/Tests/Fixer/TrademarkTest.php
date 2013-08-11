<?php
namespace JoliTypo\Tests\Fixer;

use JoliTypo\Fixer;

class TrademarkTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleString()
    {
        $fixer = new Fixer\Trademark();
        $this->assertInstanceOf('JoliTypo\Fixer\Trademark', $fixer);

        $this->assertEquals("Test", $fixer->fix("Test"));
        $this->assertEquals("(c(", $fixer->fix("(c("));
        $this->assertEquals("©", $fixer->fix("(c)"));
        $this->assertEquals("Protip©", $fixer->fix("Protip(c)"));
        $this->assertEquals("Protip ©.", $fixer->fix("Protip (c)."));
        $this->assertEquals("©®™.", $fixer->fix("(c)(r)(tm)."));
        $this->assertEquals("©®™.", $fixer->fix("(C)(R)(TM)."));
        $this->assertEquals("©".Fixer::NO_BREAK_SPACE."2013 Acme Corp™", $fixer->fix("(C) 2013 Acme Corp(TM)"));
    }

    /**
     * :-( :sadface:
     */
    public function testImpossible()
    {
        $this->markTestSkipped("Those tests can't pass: they are edge case JoliTypo does not cover ATM. Feel free to fix!");

        $fixer = new Fixer\Trademark();

        // Reference, section like this:
        $this->assertEquals("Section 12(c)", $fixer->fix('Section 12(c)'));
    }
}

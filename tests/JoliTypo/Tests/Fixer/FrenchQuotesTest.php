<?php
namespace JoliTypo\Tests\Fixer;

use JoliTypo\Fixer;

class FrenchQuotesTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleString()
    {
        $fixer = new \JoliTypo\Fixer\FrenchQuotes();
        $this->assertInstanceOf('JoliTypo\Fixer\FrenchQuotes', $fixer);

        $this->assertEquals("«".Fixer::NO_BREAK_SPACE."Good code is like a good joke.".Fixer::NO_BREAK_SPACE."»", $fixer->fix('"Good code is like a good joke."'));
        $this->assertEquals("«".Fixer::NO_BREAK_SPACE."Good code is like a Bieber.".Fixer::NO_BREAK_SPACE."» - said no ever, ever.", $fixer->fix('"Good code is like a Bieber." - said no ever, ever.'));

        $this->assertEquals("Some people are like «".Fixer::NO_BREAK_SPACE."Batman".Fixer::NO_BREAK_SPACE."», others like «".Fixer::NO_BREAK_SPACE."Superman".Fixer::NO_BREAK_SPACE."».", $fixer->fix('Some people are like "Batman", others like "Superman".'));
        $this->assertEquals('Oh my god, this quote is alone: " !', $fixer->fix('Oh my god, this quote is alone: " !'));
    }

    public function testFalsePositives()
    {
        $fixer = new \JoliTypo\Fixer\FrenchQuotes();

        $this->assertEquals('This is a time: 2"44\'.', $fixer->fix('This is a time: 2"44\'.'));
    }

    /**
     * :-( :sadface:
     */
    public function testImpossibles()
    {
        $this->markTestSkipped("Those tests can't pass: they are edge case JoliTypo does not cover ATM. Feel free to fix!");

        $fixer = new \JoliTypo\Fixer\FrenchQuotes();

        $this->assertEquals("Oh my god, this quote is alone: \" ! But those are «".Fixer::NO_BREAK_SPACE."ok".Fixer::NO_BREAK_SPACE."».", $fixer->fix('Oh my god, this quote is alone: " ! But those are "ok".'));
    }
}

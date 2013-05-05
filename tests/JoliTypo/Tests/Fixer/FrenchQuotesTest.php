<?php
namespace JoliTypo\Tests\Fixer;

class FrenchQuotesTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleString()
    {
        $fixer = new \JoliTypo\Fixer\FrenchQuotes();
        $this->assertInstanceOf('JoliTypo\Fixer\FrenchQuotes', $fixer);

        $this->assertEquals("« Good code is like a good joke. »", $fixer->fix('"Good code is like a good joke."'));
        $this->assertEquals("« Good code is like a Bieber. » - said no ever, ever.", $fixer->fix('"Good code is like a Bieber." - said no ever, ever.'));

        $this->assertEquals("Some people are like « Batman », others like « Superman ».", $fixer->fix('Some people are like "Batman", others like "Superman".'));
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

        $this->assertEquals("Am I an ellipsis…", $fixer->fix('Oh my god, this quote is alone: " ! But those are "ok".'));
    }
}

<?php
namespace JoliTypo\Tests\Fixer;

use JoliTypo\Fixer;

class EnglishQuotesTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleString()
    {
        $fixer = new Fixer\EnglishQuotes();
        $this->assertInstanceOf('JoliTypo\Fixer\EnglishQuotes', $fixer);

        $this->assertEquals("“I am smart”", $fixer->fix('"I am smart"'));
        $this->assertEquals("Quote say: “I am smart”", $fixer->fix('Quote say: "I am smart"'));
        $this->assertEquals("I'm not a “QUOTE”. Or a “US QUOTE.”", $fixer->fix('I\'m not a "QUOTE". Or a "US QUOTE."'));
        $this->assertEquals("I'm not a (“QUOTE”. Or a “US QUOTE.”)", $fixer->fix('I\'m not a ("QUOTE". Or a "US QUOTE.")'));
    }

    public function testFalsePositives()
    {
        $fixer = new Fixer\EnglishQuotes();

        $this->assertEquals('This is a time: 2"44\'.', $fixer->fix('This is a time: 2"44\'.'));
        $this->assertEquals('2"44\'.', $fixer->fix('2"44\'.'));
    }
}

<?php
namespace JoliTypo\Tests\Fixer;

use JoliTypo\Fixer;

class CurlyQuoteTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleString()
    {
        $fixer = new Fixer\CurlyQuote();
        $this->assertInstanceOf('JoliTypo\Fixer\CurlyQuote', $fixer);

        $this->assertEquals("This text in which there is a quote: I’m SUPERMAN.", $fixer->fix("This text in which there is a quote: I'm SUPERMAN."));
        $this->assertEquals("Swag’", $fixer->fix("Swag'"));
        $this->assertEquals("Swag’ me.", $fixer->fix("Swag' me."));
        $this->assertEquals("J’aime la typographie.", $fixer->fix("J'aime la typographie."));
        $this->assertEquals("Qu’est ce que l’univers ?", $fixer->fix("Qu'est ce que l'univers ?"));
    }

    public function testFalsePositives()
    {
        $fixer = new Fixer\CurlyQuote();

        $this->assertEquals("She’s 6' 10\".", $fixer->fix("She's 6' 10\"."));
        $this->assertEquals('This is a time: 2"44\'.', $fixer->fix('This is a time: 2"44\'.'));
        $this->assertEquals("Here is a crying smiley: :'(", $fixer->fix("Here is a crying smiley: :'("));
    }
}

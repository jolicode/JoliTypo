<?php
namespace JoliTypo\Tests\Fixer;

/**
 * /!\ Warning, setup your editor to see a diff between " " and " ",
 * those spaces are different! The first one is &nbsp;.
 *
 * @package JoliTypo\Tests\Fixer
 */
class FrenchNoBreakSpaceTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleString()
    {
        $fixer = new \JoliTypo\Fixer\FrenchNoBreakSpace();
        $this->assertInstanceOf('JoliTypo\Fixer\FrenchNoBreakSpace', $fixer);

        $this->assertEquals("Superman !", $fixer->fix("Superman !"));
        $this->assertEquals("Superman ?", $fixer->fix("Superman ?"));
        $this->assertEquals("Superman !?", $fixer->fix("Superman !?"));
        $this->assertEquals("Superman ‽", $fixer->fix("Superman ‽"));
        $this->assertEquals("Superman ? Nope.", $fixer->fix("Superman ? Nope."));
        $this->assertEquals("Superman : the movie", $fixer->fix("Superman : the movie"));
        $this->assertEquals("Superman: the movie", $fixer->fix("Superman: the movie"));
        $this->assertEquals("Superman ; the movie", $fixer->fix("Superman ; the movie"));
        $this->assertEquals("Superman ; the movie", $fixer->fix("Superman ; the movie"));
    }
}

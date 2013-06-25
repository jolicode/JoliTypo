<?php
namespace JoliTypo\Tests\Fixer;

use JoliTypo\Fixer;

/**
 * @package JoliTypo\Tests\Fixer
 */
class FrenchNoBreakSpaceTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleString()
    {
        $fixer = new Fixer\FrenchNoBreakSpace();
        $this->assertInstanceOf('JoliTypo\Fixer\FrenchNoBreakSpace', $fixer);

        $this->assertEquals("Superman".Fixer::NO_BREAK_THIN_SPACE."!", $fixer->fix("Superman !"));
        $this->assertEquals("Superman".Fixer::NO_BREAK_THIN_SPACE."?", $fixer->fix("Superman ?"));
        $this->assertEquals("Superman".Fixer::NO_BREAK_THIN_SPACE."!?", $fixer->fix("Superman !?"));
        $this->assertEquals("Superman".Fixer::NO_BREAK_THIN_SPACE."? Nope.", $fixer->fix("Superman ? Nope."));
        $this->assertEquals("Superman".Fixer::NO_BREAK_SPACE.": the movie", $fixer->fix("Superman : the movie"));
        $this->assertEquals("Superman: the movie", $fixer->fix("Superman: the movie"));
        $this->assertEquals("Superman".Fixer::NO_BREAK_THIN_SPACE."; the movie", $fixer->fix("Superman ; the movie"));
        $this->assertEquals("Superman".Fixer::NO_BREAK_THIN_SPACE."; the movie", $fixer->fix("Superman ; the movie"));
        $this->assertEquals("fdda:5cc1:23:4::1f", $fixer->fix("fdda:5cc1:23:4::1f"));

        $this->assertEquals("Here is a  brand name: Yahoo!", $fixer->fix("Here is a  brand name: Yahoo!"));
    }
}

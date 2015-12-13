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

        $this->assertEquals("9".Fixer::NO_BREAK_SPACE."h", $fixer->fix("9 h"));
        $this->assertEquals("42".Fixer::NO_BREAK_SPACE."minutes", $fixer->fix("42 minutes"));
        $this->assertEquals("0".Fixer::NO_BREAK_SPACE."seconde", $fixer->fix("0 seconde"));
        $this->assertEquals("13".Fixer::NO_BREAK_SPACE."heures".Fixer::NO_BREAK_SPACE."37".Fixer::NO_BREAK_SPACE."minutes", $fixer->fix("13 heures 37 minutes"));
        $this->assertEquals("13".Fixer::NO_BREAK_SPACE."h".Fixer::NO_BREAK_SPACE."37".Fixer::NO_BREAK_SPACE."min", $fixer->fix("13h37min"));
        $this->assertEquals("23".Fixer::NO_BREAK_SPACE."h".Fixer::NO_BREAK_SPACE."59".Fixer::NO_BREAK_SPACE."min".Fixer::NO_BREAK_SPACE."59".Fixer::NO_BREAK_SPACE."s", $fixer->fix("23 h 59 min 59 s"));
        $this->assertEquals("42".Fixer::NO_BREAK_SPACE."mg", $fixer->fix("42 mg"));
        $this->assertEquals("12".Fixer::NO_BREAK_SPACE."km".Fixer::NO_BREAK_SPACE."500", $fixer->fix("12 km 500"));
        $this->assertEquals("12,5".Fixer::NO_BREAK_SPACE."km", $fixer->fix("12,5 km"));
        $this->assertEquals("37,2".Fixer::NO_BREAK_SPACE."°C", $fixer->fix("37,2 °C"));
        $this->assertEquals("19,99".Fixer::NO_BREAK_SPACE."dollars", $fixer->fix("19,99 dollars"));

        $this->assertEquals("250 spectateurs", $fixer->fix("250 spectateurs")); // TODO: “spectateurs” is a kind of unit. The space could be replaced by a no break space
    }
}

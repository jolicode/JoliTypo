<?php
namespace JoliTypo\Tests\Fixer;

use JoliTypo\Fixer;

/**
 * @package JoliTypo\Tests\Fixer
 */
class NoSpaceBeforeCommaTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleString()
    {
        $fixer = new Fixer\NoSpaceBeforeComma();
        $this->assertInstanceOf('JoliTypo\Fixer\NoSpaceBeforeComma', $fixer);

        $this->assertEquals("Superman, you're my hero", $fixer->fix("Superman,you're my hero"));
        $this->assertEquals("Superman, you're my hero", $fixer->fix("Superman ,you're my hero"));
        $this->assertEquals("Superman, you're my hero", $fixer->fix("Superman  ,  you're my hero"));
    }
}

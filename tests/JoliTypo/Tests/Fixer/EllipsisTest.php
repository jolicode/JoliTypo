<?php
namespace JoliTypo\Tests\Fixer;

use JoliTypo\Fixer;

class EllipsisTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleString()
    {
        $fixer = new Fixer\Ellipsis();
        $this->assertInstanceOf('JoliTypo\Fixer\Ellipsis', $fixer);

        $this->assertEquals("Test", $fixer->fix("Test"));
        $this->assertEquals("Test…", $fixer->fix("Test..."));
        $this->assertEquals("Am I an ellipsis?", $fixer->fix("Am I an ellipsis?"));
        $this->assertEquals("Am I an ellipsis..", $fixer->fix("Am I an ellipsis.."));
        $this->assertEquals("Am I an ellipsis…", $fixer->fix("Am I an ellipsis...."));
        $this->assertEquals("Am I an ellipsis…", $fixer->fix("Am I an ellipsis…"));
        $this->assertEquals("Am I an ellipsis… With following text.", $fixer->fix("Am I an ellipsis... With following text."));
    }
}

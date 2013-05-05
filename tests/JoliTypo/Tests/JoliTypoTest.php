<?php
namespace JoliTypo\Tests;

class JoliTypoTest extends \PHPUnit_Framework_TestCase
{
    public function testRegisterProvider()
    {
        $fixer = new \JoliTypo\Fixer();
        $this->assertInstanceOf('JoliTypo\Fixer', $fixer);

        $this->assertEquals("Coucou&hellip;", $fixer->fix("Coucou..."));
        $this->assertEquals("Coucou…", $fixer->fix("Coucou..."));
    }
}

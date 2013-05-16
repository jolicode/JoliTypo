<?php
namespace JoliTypo\Tests\Fixer;

use JoliTypo\Fixer;

class HyphenTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleString()
    {
        $fixer = new Fixer\Hyphen('fr');
        $this->assertInstanceOf('JoliTypo\Fixer\Hyphen', $fixer);

        $this->assertEquals("Test", $fixer->fix("Test"));
    }
}

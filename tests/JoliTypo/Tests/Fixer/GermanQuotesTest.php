<?php
namespace JoliTypo\Tests\Fixer;

use JoliTypo\Fixer;

class GermanQuotesTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleString()
    {
        $fixer = new Fixer\GermanQuotes();
        $this->assertInstanceOf('JoliTypo\Fixer\GermanQuotes', $fixer);

        $this->assertEquals("„I am smart“", $fixer->fix('"I am smart"'));
        $this->assertEquals("(„I am smart“)", $fixer->fix('("I am smart")'));
        $this->assertEquals("Andreas fragte mich: „Hast du den Artikel 'EU-Erweiterung' gelesen?“", $fixer->fix('Andreas fragte mich: "Hast du den Artikel \'EU-Erweiterung\' gelesen?"'));
    }

    public function testFalsePositives()
    {
        $fixer = new Fixer\GermanQuotes();

        $this->assertEquals('This is a time: 2"44\'.', $fixer->fix('This is a time: 2"44\'.'));
        $this->assertEquals('2"44\'.', $fixer->fix('2"44\'.'));
    }
}

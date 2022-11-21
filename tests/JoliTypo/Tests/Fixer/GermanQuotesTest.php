<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo\Tests\Fixer;

use JoliTypo\Fixer;
use PHPUnit\Framework\TestCase;

class GermanQuotesTest extends TestCase
{
    public function testSimpleString()
    {
        $fixer = new Fixer\GermanQuotes();
        $this->assertInstanceOf('JoliTypo\Fixer\GermanQuotes', $fixer);

        $this->basicStringsAsserts($fixer);
    }

    public function testSmartQuoteConfig()
    {
        $fixer = new Fixer\SmartQuotes('de');

        $this->basicStringsAsserts($fixer);
    }

    public function testFalsePositives()
    {
        $fixer = new Fixer\GermanQuotes();

        $this->assertSame('This is a time: 2"44\'.', $fixer->fix('This is a time: 2"44\'.'));
        $this->assertSame('2"44\'.', $fixer->fix('2"44\'.'));
    }

    protected function basicStringsAsserts($fixer)
    {
        $this->assertSame('„I am smart“', $fixer->fix('"I am smart"'));
        $this->assertSame('(„I am smart“)', $fixer->fix('("I am smart")'));
        $this->assertSame("Andreas fragte mich: „Hast du den Artikel 'EU-Erweiterung' gelesen?“", $fixer->fix('Andreas fragte mich: "Hast du den Artikel \'EU-Erweiterung\' gelesen?"'));
    }
}

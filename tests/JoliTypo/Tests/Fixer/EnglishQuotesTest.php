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

class EnglishQuotesTest extends TestCase
{
    public function testSimpleString()
    {
        $fixer = new Fixer\EnglishQuotes();
        $this->assertInstanceOf('JoliTypo\Fixer\EnglishQuotes', $fixer);

        $this->basicStringsAsserts($fixer);
    }

    public function testSmartQuoteConfig()
    {
        $fixer = new Fixer\SmartQuotes('en');

        $this->basicStringsAsserts($fixer);
    }

    public function testFalsePositives()
    {
        $fixer = new Fixer\EnglishQuotes();

        $this->assertEquals('This is a time: 2"44\'.', $fixer->fix('This is a time: 2"44\'.'));
        $this->assertEquals('2"44\'.', $fixer->fix('2"44\'.'));
    }

    protected function basicStringsAsserts($fixer)
    {
        $this->assertEquals('“I am smart”', $fixer->fix('"I am smart"'));
        $this->assertEquals('Quote say: “I am smart”', $fixer->fix('Quote say: "I am smart"'));
        $this->assertEquals("I'm not a “QUOTE”. Or a “US QUOTE.”", $fixer->fix('I\'m not a "QUOTE". Or a "US QUOTE."'));
        $this->assertEquals("I'm not a (“QUOTE”. Or a “US QUOTE.”)", $fixer->fix('I\'m not a ("QUOTE". Or a "US QUOTE.")'));
    }
}

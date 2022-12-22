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
    public function testSimpleString(): void
    {
        $fixer = new Fixer\EnglishQuotes();
        $this->assertInstanceOf('JoliTypo\Fixer\EnglishQuotes', $fixer);

        $this->basicStringsAsserts($fixer);
    }

    public function testSmartQuoteConfig(): void
    {
        $fixer = new Fixer\SmartQuotes('en');

        $this->basicStringsAsserts($fixer);
    }

    public function testFalsePositives(): void
    {
        $fixer = new Fixer\EnglishQuotes();

        $this->assertSame('This is a time: 2"44\'.', $fixer->fix('This is a time: 2"44\'.'));
        $this->assertSame('2"44\'.', $fixer->fix('2"44\'.'));
    }

    protected function basicStringsAsserts($fixer): void
    {
        $this->assertSame('“I am smart”', $fixer->fix('"I am smart"'));
        $this->assertSame('Quote say: “I am smart”', $fixer->fix('Quote say: "I am smart"'));
        $this->assertSame("I'm not a “QUOTE”. Or a “US QUOTE.”", $fixer->fix('I\'m not a "QUOTE". Or a "US QUOTE."'));
        $this->assertSame("I'm not a (“QUOTE”. Or a “US QUOTE.”)", $fixer->fix('I\'m not a ("QUOTE". Or a "US QUOTE.")'));
    }
}

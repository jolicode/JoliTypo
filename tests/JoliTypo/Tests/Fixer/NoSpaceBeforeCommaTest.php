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

class NoSpaceBeforeCommaTest extends TestCase
{
    public function testSimpleString(): void
    {
        $fixer = new Fixer\NoSpaceBeforeComma();
        $this->assertInstanceOf('JoliTypo\Fixer\NoSpaceBeforeComma', $fixer);

        $this->assertSame("Superman, you're my hero", $fixer->fix("Superman,you're my hero"));
        $this->assertSame("Superman, you're my hero", $fixer->fix("Superman ,you're my hero"));
        $this->assertSame("Superman, you're my hero", $fixer->fix("Superman  ,  you're my hero"));
        $this->assertSame('F, bar', $fixer->fix('F,bar'));
        $this->assertSame('Seule 1,7 million de personnes', $fixer->fix('Seule 1,7 million de personnes'));

        // Tabs are spaces, but line breaks are not (#88)
        $this->assertSame("Superman, you're my hero", $fixer->fix("Superman\t,\tyou're my hero"));
        $this->assertSame("Superman,\nyou're my hero", $fixer->fix("Superman,\nyou're my hero"));
        $this->assertSame("Superman,\nyou're my hero", $fixer->fix("Superman ,\nyou're my hero"));
        $this->assertSame("Superman, \nyou're my hero", $fixer->fix("Superman, \nyou're my hero"));

        // A pipe is not a space (#133)
        $this->assertSame('a, |b', $fixer->fix('a,|b'));
        $this->assertSame('a|, b', $fixer->fix('a| , b'));
    }
}

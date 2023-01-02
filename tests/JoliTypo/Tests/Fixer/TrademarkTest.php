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

class TrademarkTest extends TestCase
{
    public function testSimpleString(): void
    {
        $fixer = new Fixer\Trademark();
        $this->assertInstanceOf('JoliTypo\Fixer\Trademark', $fixer);

        $this->assertSame('Test', $fixer->fix('Test'));
        $this->assertSame('(c(', $fixer->fix('(c('));
        $this->assertSame('©', $fixer->fix('(c)'));
        $this->assertSame('Protip©', $fixer->fix('Protip(c)'));
        $this->assertSame('Protip ©.', $fixer->fix('Protip (c).'));
        $this->assertSame('©®™.', $fixer->fix('(c)(r)(tm).'));
        $this->assertSame('©®™.', $fixer->fix('(C)(R)(TM).'));
        $this->assertSame('©' . Fixer::NO_BREAK_SPACE . '2013 Acme Corp™', $fixer->fix('(C) 2013 Acme Corp(TM)'));
    }

    /**
     * :-( :sadface:.
     */
    public function testImpossible(): void
    {
        $this->markTestSkipped("Those tests can't pass: they are edge case JoliTypo does not cover ATM. Feel free to fix!");

        $fixer = new Fixer\Trademark();

        // Reference, section like this:
        $this->assertSame('Section 12(c)', $fixer->fix('Section 12(c)'));
    }
}

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

class DashTest extends TestCase
{
    public function testSimpleString(): void
    {
        $fixer = new Fixer\Dash();
        $this->assertInstanceOf('JoliTypo\Fixer\Dash', $fixer);

        $this->assertSame('Test', $fixer->fix('Test'));
        $this->assertSame('M. Jackson: 1964' . Fixer::NDASH . '2009', $fixer->fix('M. Jackson: 1964-2009'));
        $this->assertSame('M. Jackson: 1964 ' . Fixer::NDASH . ' 2009', $fixer->fix('M. Jackson: 1964 - 2009'));
        $this->assertSame('Style ' . Fixer::NDASH . ' not sincerity ' . Fixer::NDASH . ' is the vital thing.', $fixer->fix('Style - not sincerity - is the vital thing.'));
        // $this->assertSame("Style ".Fixer::MDASH." not sincerity ".Fixer::MDASH." is the vital thing.", $fixer->fix("Style -not sincerity- is the vital thing."));

        $this->assertSame('Style' . Fixer::MDASH . 'you have it.', $fixer->fix('Style -- you have it.'));
        $this->assertSame('Style' . Fixer::MDASH . 'you have it.', $fixer->fix('Style--you have it.'));
        $this->assertSame('Style' . Fixer::MDASH . 'you have it.', $fixer->fix('Style-- you have it.'));
    }
}

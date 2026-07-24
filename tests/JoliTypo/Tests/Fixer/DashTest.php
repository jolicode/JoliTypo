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
        // Hyphen between numbers: converted to en dash, no spaces added
        $this->assertSame('M. Jackson: 1964' . Fixer::NDASH . '2009', $fixer->fix('M. Jackson: 1964-2009'));
        // Space before en dash becomes narrow no-break space; space after remains breakable
        $this->assertSame('M. Jackson: 1964' . Fixer::NO_BREAK_THIN_SPACE . Fixer::NDASH . ' 2009', $fixer->fix('M. Jackson: 1964 - 2009'));
        $this->assertSame('Style' . Fixer::NO_BREAK_THIN_SPACE . Fixer::NDASH . ' not sincerity' . Fixer::NO_BREAK_THIN_SPACE . Fixer::NDASH . ' is the vital thing.', $fixer->fix('Style - not sincerity - is the vital thing.'));
        // $this->assertSame("Style ".Fixer::MDASH." not sincerity ".Fixer::MDASH." is the vital thing.", $fixer->fix("Style -not sincerity- is the vital thing."));

        // Double hyphens: converted to em dash (spaces stripped by the conversion rule)
        $this->assertSame('Style' . Fixer::MDASH . 'you have it.', $fixer->fix('Style -- you have it.'));
        $this->assertSame('Style' . Fixer::MDASH . 'you have it.', $fixer->fix('Style--you have it.'));
        $this->assertSame('Style' . Fixer::MDASH . 'you have it.', $fixer->fix('Style-- you have it.'));
    }

    public function testDashSpacing(): void
    {
        $fixer = new Fixer\Dash();

        // En dash already in text with a space before: space becomes narrow no-break space
        $this->assertSame('text' . Fixer::NO_BREAK_THIN_SPACE . Fixer::NDASH . ' more text', $fixer->fix('text ' . Fixer::NDASH . ' more text'));

        // Em dash already in text with a space before: space becomes narrow no-break space
        $this->assertSame('text' . Fixer::NO_BREAK_THIN_SPACE . Fixer::MDASH . ' more text', $fixer->fix('text ' . Fixer::MDASH . ' more text'));

        // No space before dash: not changed
        $this->assertSame('text' . Fixer::NDASH . 'more', $fixer->fix('text' . Fixer::NDASH . 'more'));
        $this->assertSame('text' . Fixer::MDASH . 'more', $fixer->fix('text' . Fixer::MDASH . 'more'));

        // Already a narrow no-break space before dash: normalised (no double space)
        $this->assertSame('text' . Fixer::NO_BREAK_THIN_SPACE . Fixer::NDASH . ' more', $fixer->fix('text' . Fixer::NO_BREAK_THIN_SPACE . Fixer::NDASH . ' more'));

        // Already a no-break space before dash: replaced with narrow no-break space
        $this->assertSame('text' . Fixer::NO_BREAK_THIN_SPACE . Fixer::NDASH . ' more', $fixer->fix('text' . Fixer::NO_BREAK_SPACE . Fixer::NDASH . ' more'));
    }
}

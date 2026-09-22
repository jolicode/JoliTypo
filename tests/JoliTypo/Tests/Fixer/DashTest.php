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
    private const FINE = Fixer::NO_BREAK_THIN_SPACE;

    public function testSimpleString(): void
    {
        $fixer = new Fixer\Dash();
        $this->assertInstanceOf('JoliTypo\Fixer\Dash', $fixer);

        $this->assertSame('Test', $fixer->fix('Test'));
        // Hyphen between numbers: converted to en dash, no spaces added
        $this->assertSame('M. Jackson: 1964' . Fixer::NDASH . '2009', $fixer->fix('M. Jackson: 1964-2009'));
        // A range holds together, so both of its spaces are narrow and no-break
        $this->assertSame('M. Jackson: 1964' . self::FINE . Fixer::NDASH . self::FINE . '2009', $fixer->fix('M. Jackson: 1964 - 2009'));
        // A pair of dashes marks an incise: the first binds forward, the second backward
        $this->assertSame('Style ' . Fixer::NDASH . self::FINE . 'not sincerity' . self::FINE . Fixer::NDASH . ' is the vital thing.', $fixer->fix('Style - not sincerity - is the vital thing.'));
        // $this->assertSame("Style ".Fixer::MDASH." not sincerity ".Fixer::MDASH." is the vital thing.", $fixer->fix("Style -not sincerity- is the vital thing."));

        // Double hyphens: converted to em dash (spaces stripped by the conversion rule)
        $this->assertSame('Style' . Fixer::MDASH . 'you have it.', $fixer->fix('Style -- you have it.'));
        $this->assertSame('Style' . Fixer::MDASH . 'you have it.', $fixer->fix('Style--you have it.'));
        $this->assertSame('Style' . Fixer::MDASH . 'you have it.', $fixer->fix('Style-- you have it.'));
    }

    public function testDashSpacing(): void
    {
        $fixer = new Fixer\Dash();

        // A lone dash cannot be told from a plain separator, so it binds to what precedes it
        $this->assertSame('text' . self::FINE . Fixer::NDASH . ' more text', $fixer->fix('text ' . Fixer::NDASH . ' more text'));
        $this->assertSame('text' . self::FINE . Fixer::MDASH . ' more text', $fixer->fix('text ' . Fixer::MDASH . ' more text'));

        // No space before dash: not changed
        $this->assertSame('text' . Fixer::NDASH . 'more', $fixer->fix('text' . Fixer::NDASH . 'more'));
        $this->assertSame('text' . Fixer::MDASH . 'more', $fixer->fix('text' . Fixer::MDASH . 'more'));

        // Already a narrow no-break space before dash: normalised (no double space)
        $this->assertSame('text' . self::FINE . Fixer::NDASH . ' more', $fixer->fix('text' . self::FINE . Fixer::NDASH . ' more'));

        // Already a no-break space before dash: replaced with narrow no-break space
        $this->assertSame('text' . self::FINE . Fixer::NDASH . ' more', $fixer->fix('text' . Fixer::NO_BREAK_SPACE . Fixer::NDASH . ' more'));
    }

    public function testIncise(): void
    {
        $fixer = new Fixer\Dash();

        // The opening dash of an incise binds to what follows, the closing one to what precedes
        $this->assertSame(
            'En dashes ' . Fixer::NDASH . self::FINE . 'when used as incises' . self::FINE . Fixer::NDASH . ' need no-break spaces.',
            $fixer->fix('En dashes ' . Fixer::NDASH . ' when used as incises ' . Fixer::NDASH . ' need no-break spaces.')
        );

        // Em dashes mark incises as well
        $this->assertSame(
            'An incise ' . Fixer::MDASH . self::FINE . 'like this one' . self::FINE . Fixer::MDASH . ' is a pair.',
            $fixer->fix('An incise ' . Fixer::MDASH . ' like this one ' . Fixer::MDASH . ' is a pair.')
        );

        // An odd dash left over is not a pair, so it keeps the default spacing
        $this->assertSame(
            'One ' . Fixer::NDASH . self::FINE . 'incise' . self::FINE . Fixer::NDASH . ' then a lone dash' . self::FINE . Fixer::NDASH . ' here.',
            $fixer->fix('One ' . Fixer::NDASH . ' incise ' . Fixer::NDASH . ' then a lone dash ' . Fixer::NDASH . ' here.')
        );

        // A dash without a space on both sides opens nothing
        $this->assertSame(
            'Style' . self::FINE . Fixer::NDASH . 'you have it' . self::FINE . Fixer::NDASH . ' always.',
            $fixer->fix('Style ' . Fixer::NDASH . 'you have it ' . Fixer::NDASH . ' always.')
        );

        // Running the fixer twice changes nothing more
        $once = $fixer->fix('Style ' . Fixer::NDASH . ' not sincerity ' . Fixer::NDASH . ' is the vital thing.');
        $this->assertSame($once, $fixer->fix($once));
    }

    public function testRange(): void
    {
        $fixer = new Fixer\Dash();

        // A range opens and closes nothing: the same no-break space on both sides
        $this->assertSame('1978' . self::FINE . Fixer::NDASH . self::FINE . '1994', $fixer->fix('1978 ' . Fixer::NDASH . ' 1994'));
        $this->assertSame('1978' . self::FINE . Fixer::MDASH . self::FINE . '1994', $fixer->fix('1978 ' . Fixer::MDASH . ' 1994'));

        // A tight range is left alone, no space is ever inserted
        $this->assertSame('1978' . Fixer::NDASH . '1994', $fixer->fix('1978' . Fixer::NDASH . '1994'));

        // A range is not half of an incise pair
        $this->assertSame(
            'He lived 1978' . self::FINE . Fixer::NDASH . self::FINE . '1994 and wrote' . self::FINE . Fixer::NDASH . ' a lot.',
            $fixer->fix('He lived 1978 ' . Fixer::NDASH . ' 1994 and wrote ' . Fixer::NDASH . ' a lot.')
        );

        // Running the fixer twice changes nothing more
        $once = $fixer->fix('M. Jackson: 1964 - 2009');
        $this->assertSame($once, $fixer->fix($once));
    }
}

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

class UnitTest extends TestCase
{
    public function testNumericUnits(): void
    {
        $fixer = new Fixer\Unit();
        $this->assertInstanceOf('JoliTypo\Fixer\Unit', $fixer);

        $this->assertSame('Test', $fixer->fix('Test'));
        $this->assertSame('1' . Fixer::NO_BREAK_SPACE . 'h', $fixer->fix('1 h'));
        $this->assertSame('2' . Fixer::NO_BREAK_SPACE . '฿', $fixer->fix('2 ฿'));
        $this->assertSame('3' . Fixer::NO_BREAK_SPACE . 'things', $fixer->fix('3 things'));
        $this->assertSame('4,5' . Fixer::NO_BREAK_SPACE . 'km', $fixer->fix('4,5 km'));
        $this->assertSame('5.1' . Fixer::NO_BREAK_SPACE . 'deg', $fixer->fix('5.1 deg'));
        $this->assertSame('6' . Fixer::NO_BREAK_SPACE . 'ºC', $fixer->fix('6 ºC'));
        $this->assertSame('7' . Fixer::NO_BREAK_SPACE . '$', $fixer->fix('7 $'));
        $this->assertSame('8º' . Fixer::NO_BREAK_SPACE . '18′ 30″', $fixer->fix('8º 18′ 30″'));
        $this->assertSame('9' . Fixer::NO_BREAK_SPACE . 'hours', $fixer->fix('9 hours'));
        $this->assertSame('10e position', $fixer->fix('10e position')); // We can't fix this reliably
        $this->assertSame('nº' . Fixer::NO_BREAK_SPACE . '11', $fixer->fix('nº 11'));
        $this->assertSame('12' . Fixer::NO_BREAK_SPACE . '%', $fixer->fix('12 %'));
        $this->assertSame('13' . Fixer::NO_BREAK_SPACE . 'Ω', $fixer->fix('13 Ω'));
        $this->assertSame('14' . Fixer::NO_BREAK_SPACE . 'Ω', $fixer->fix('14' . Fixer::NO_BREAK_THIN_SPACE . 'Ω'));

        // Symbols and emoji, like words (#151)
        $this->assertSame('3' . Fixer::NO_BREAK_SPACE . '🍕', $fixer->fix('3 🍕'));
        $this->assertSame('2' . Fixer::NO_BREAK_SPACE . "\u{2764}\u{FE0F}", $fixer->fix("2 \u{2764}\u{FE0F}"));
        $this->assertSame('4' . Fixer::NO_BREAK_SPACE . "\u{1F469}\u{200D}\u{1F4BB}", $fixer->fix("4 \u{1F469}\u{200D}\u{1F4BB}"));
        $this->assertSame('5' . Fixer::NO_BREAK_SPACE . '★', $fixer->fix('5 ★'));
        $this->assertSame('1 → 2', $fixer->fix('1 → 2'));

        // Tabs are spaces, but line breaks are not (#88)
        $this->assertSame('15' . Fixer::NO_BREAK_SPACE . 'kg', $fixer->fix("15\tkg"));
        $this->assertSame("16\nkg", $fixer->fix("16\nkg"));
        $this->assertSame("17\r\nkg", $fixer->fix("17\r\nkg"));
        $this->assertSame("## Verrückt und Harakiri 2\nAm Tag nach dem", $fixer->fix("## Verrückt und Harakiri 2\nAm Tag nach dem"));
    }

    /**
     * @see https://github.com/jolicode/JoliTypo/issues/57
     */
    public function testAlreadyFixedContentIsLeftUntouched(): void
    {
        $fixer = new Fixer\Unit();

        // Without the "u" modifier, the first byte of the no-break space was taken for "º" and its second byte for a space,
        // which produced invalid UTF-8
        $this->assertSame(Fixer::LAQUO . Fixer::NO_BREAK_SPACE . 'très', $fixer->fix(Fixer::LAQUO . Fixer::NO_BREAK_SPACE . 'très'));
        $this->assertSame('n°' . Fixer::NO_BREAK_SPACE . '5', $fixer->fix('n°' . Fixer::NO_BREAK_SPACE . '5'));
        $this->assertSame('4' . Fixer::NO_BREAK_SPACE . 'm', $fixer->fix('4' . Fixer::NO_BREAK_SPACE . 'm'));
    }
}

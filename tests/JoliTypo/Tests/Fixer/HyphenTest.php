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

class HyphenTest extends TestCase
{
    public function testSimpleString(): void
    {
        $fixer = new Fixer\Hyphen('fr');
        $this->assertInstanceOf('JoliTypo\Fixer\Hyphen', $fixer);

        $this->assertSame('Test', $fixer->fix('Test'));
        $this->assertSame('Cordia' . Fixer::SHY . 'le' . Fixer::SHY . 'ment', $fixer->fix('Cordialement'));
        $this->assertSame('Cordia' . Fixer::SHY . 'le' . Fixer::SHY . 'ment' . Fixer::NO_BREAK_THIN_SPACE . '!', $fixer->fix('Cordialement' . Fixer::NO_BREAK_THIN_SPACE . '!'));
    }

    public function testLocaleFallback(): void
    {
        $fixer = new Fixer\Hyphen('fr_BE');
        $this->assertInstanceOf('JoliTypo\Fixer\Hyphen', $fixer);

        $this->assertSame('Test', $fixer->fix('Test'));
        $this->assertSame('Cordia' . Fixer::SHY . 'le' . Fixer::SHY . 'ment', $fixer->fix('Cordialement'));
    }

    public function testNonExistingLocale(): void
    {
        $fixer = new Fixer\Hyphen('toto');

        $this->assertSame('Test', $fixer->fix('Test'));
        $this->assertSame('Cordialement', $fixer->fix('Cordialement'));
    }
}

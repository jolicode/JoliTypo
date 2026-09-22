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

    public function testLeftMinOption(): void
    {
        $fixer = new Fixer\Hyphen('fr', leftMin: 8);

        $this->assertSame('Cordiale' . Fixer::SHY . 'ment', $fixer->fix('Cordialement'));
    }

    public function testRightMinOption(): void
    {
        $fixer = new Fixer\Hyphen('fr', rightMin: 5);

        $this->assertSame('Cordia' . Fixer::SHY . 'lement', $fixer->fix('Cordialement'));
    }

    public function testWordMinOption(): void
    {
        $fixer = new Fixer\Hyphen('fr', wordMin: 13);

        $this->assertSame('Cordialement', $fixer->fix('Cordialement'));
        $this->assertSame(self::hyphenated('Anti', 'cons', 'ti', 'tu', 'tion', 'nel', 'le', 'ment'), $fixer->fix('Anticonstitutionnellement'));
    }

    public function testOptionsAreKeptWhenLocaleChanges(): void
    {
        $fixer = new Fixer\Hyphen('en_GB', wordMin: 13);
        $fixer->setLocale('fr');

        $this->assertSame('Cordialement', $fixer->fix('Cordialement'));
        $this->assertSame(self::hyphenated('Anti', 'cons', 'ti', 'tu', 'tion', 'nel', 'le', 'ment'), $fixer->fix('Anticonstitutionnellement'));
    }

    public function testOptionsAreKeptWhenLocaleChangesThroughFixer(): void
    {
        $fixer = new Fixer([new Fixer\Hyphen('en_GB', wordMin: 13)]);
        $fixer->setLocale('fr_FR');

        $this->assertSame('<p>Cordialement</p>', $fixer->fix('<p>Cordialement</p>'));
    }

    private static function hyphenated(string ...$syllables): string
    {
        return implode(Fixer::SHY, $syllables);
    }
}

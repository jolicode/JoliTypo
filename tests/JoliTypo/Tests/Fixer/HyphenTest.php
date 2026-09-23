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

    /**
     * @see https://github.com/jolicode/JoliTypo/issues/57
     */
    public function testAlreadyHyphenatedWordsAreLeftUntouched(): void
    {
        $fixer = new Fixer\Hyphen('fr');

        $fixed = $fixer->fix('Cordialement');
        $this->assertSame('Cordia' . Fixer::SHY . 'le' . Fixer::SHY . 'ment', $fixed);
        $this->assertSame($fixed, $fixer->fix($fixed));
        $this->assertSame($fixed, $fixer->fix($fixer->fix($fixed)));

        $fixed = $fixer->fix('Personnalisation');
        $this->assertStringContainsString(Fixer::SHY, $fixed);
        $this->assertSame($fixed, $fixer->fix($fixed));

        // Words without soft hyphen are still hyphenated, whatever the separator
        $this->assertSame(
            'Cordia' . Fixer::SHY . 'le' . Fixer::SHY . 'ment Cordia' . Fixer::SHY . 'le' . Fixer::SHY . 'ment' . Fixer::NO_BREAK_SPACE . 'Cordia' . Fixer::SHY . 'le' . Fixer::SHY . 'ment' . Fixer::NO_BREAK_THIN_SPACE . '!',
            $fixer->fix('Cordia' . Fixer::SHY . 'le' . Fixer::SHY . 'ment Cordialement' . Fixer::NO_BREAK_SPACE . 'Cordialement' . Fixer::NO_BREAK_THIN_SPACE . '!')
        );

        $fixer = new Fixer\Hyphen('en_GB');

        $fixed = $fixer->fix('Pronunciation');
        $this->assertSame('Pronun' . Fixer::SHY . 'ci' . Fixer::SHY . 'ation', $fixed);
        $this->assertSame($fixed, $fixer->fix($fixed));
    }

    /**
     * @see https://github.com/jolicode/JoliTypo/issues/57
     */
    public function testManualHyphenationIsPreserved(): void
    {
        $fixer = new Fixer\Hyphen('fr');

        $this->assertSame('anti' . Fixer::SHY . 'constitutionnellement', $fixer->fix('anti' . Fixer::SHY . 'constitutionnellement'));
    }

    private static function hyphenated(string ...$syllables): string
    {
        return implode(Fixer::SHY, $syllables);
    }
}

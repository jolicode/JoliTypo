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

class UnicodeNormalizationTest extends TestCase
{
    public function testComposesDecomposedCharacters(): void
    {
        $fixer = new Fixer\UnicodeNormalization();
        $this->assertInstanceOf('JoliTypo\Fixer\UnicodeNormalization', $fixer);

        // e + COMBINING ACUTE ACCENT => é
        $this->assertSame('été', $fixer->fix("e\u{0301}te\u{0301}"));
        // Combining marks are reordered canonically before being composed
        $this->assertSame("\u{1EC6}", $fixer->fix("E\u{0302}\u{0323}"));
        // Hangul jamo are composed into a syllable
        $this->assertSame("\u{D55C}", $fixer->fix("\u{1112}\u{1161}\u{11AB}"));
        // Mixed content
        $this->assertSame('Déjà vu, l’été à la mer.', $fixer->fix("De\u{0301}ja\u{0300} vu, l’e\u{0301}te\u{0301} a\u{0300} la mer."));
    }

    public function testLeavesComposedContentUntouched(): void
    {
        $fixer = new Fixer\UnicodeNormalization();

        $this->assertSame('', $fixer->fix(''));
        $this->assertSame('Test', $fixer->fix('Test'));
        $this->assertSame('Déjà vu, l’été à la mer.', $fixer->fix('Déjà vu, l’été à la mer.'));
    }

    public function testDoesNotApplyCompatibilityDecomposition(): void
    {
        $fixer = new Fixer\UnicodeNormalization();

        // NFKC would replace all of these, NFC must keep them
        foreach ([Fixer::NO_BREAK_SPACE, Fixer::NO_BREAK_THIN_SPACE, Fixer::ELLIPSIS, Fixer::TRADE, Fixer::SHY, '²', '½', 'µ', 'ﬁ', 'œ'] as $char) {
            $this->assertSame('a' . $char . 'b', $fixer->fix('a' . $char . 'b'));
        }
    }

    public function testKeepsInvalidUtf8Untouched(): void
    {
        $fixer = new Fixer\UnicodeNormalization();

        $this->assertSame("Caf\xE9", $fixer->fix("Caf\xE9"));
    }
}

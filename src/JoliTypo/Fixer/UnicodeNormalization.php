<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo\Fixer;

use JoliTypo\FixerInterface;
use JoliTypo\StateBag;

/**
 * Converts the text to Unicode Normalization Form C (canonical composition).
 *
 * Content pasted from PDF files, macOS or some editors can contain decomposed
 * characters: a base letter followed by combining marks, like "e" + U+0301
 * COMBINING ACUTE ACCENT instead of "é" (U+00E9). Both render the same but are
 * different strings, which breaks search, comparisons and hyphenation.
 *
 * Only the canonical form (NFC) is applied, because it is lossless. The
 * compatibility form (NFKC) is deliberately not offered: it would turn the
 * no-break spaces, ellipsis and trademark sign produced by the other fixers
 * back into their plain ASCII equivalents.
 *
 * @see https://unicode.org/reports/tr15/
 */
class UnicodeNormalization implements FixerInterface
{
    public function fix(string $content, ?StateBag $stateBag = null): string
    {
        $normalized = \Normalizer::normalize($content, \Normalizer::FORM_C);

        // Normalizer::normalize() returns false on invalid UTF-8: leave the content untouched
        return false === $normalized ? $content : $normalized;
    }
}

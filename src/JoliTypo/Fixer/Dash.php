<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo\Fixer;

use JoliTypo\Fixer;
use JoliTypo\FixerInterface;
use JoliTypo\StateBag;

class Dash implements FixerInterface
{
    /** A space that can already be there, whatever its width */
    private const SPACE = '[ ' . Fixer::NO_BREAK_SPACE . Fixer::NO_BREAK_THIN_SPACE . ']';

    private const DASH = '(?:' . Fixer::NDASH . '|' . Fixer::MDASH . ')';

    public function fix(string $content, ?StateBag $stateBag = null): string
    {
        // Convert hyphen between numbers/spaces to en dash (date/number ranges)
        $content = preg_replace('@(?<=[0-9 ]|^)-(?=[0-9 ]|$)@', Fixer::NDASH, $content) ?? $content;

        // Convert double hyphens to em dash
        $content = preg_replace('@ ?-- ?([^-]|$)@s', Fixer::MDASH . '$1', $content) ?? $content;

        // A dash used as a text separator binds to what precedes it, so that it never starts a line
        $content = preg_replace(
            '@' . self::SPACE . '(' . self::DASH . ')@u',
            Fixer::NO_BREAK_THIN_SPACE . '$1',
            $content
        ) ?? $content;

        $content = $this->bindIncises($content);

        return $this->bindRanges($content);
    }

    /**
     * A pair of dashes marks an incise: the opening one binds to what follows, the closing one to
     * what precedes, the way an opening and a closing quotation mark do.
     *
     * A dash left alone is not an incise that can be told from a plain separator, so it keeps the
     * default spacing.
     */
    private function bindIncises(string $content): string
    {
        // Dashes between numbers are ranges, not incises
        $pattern = '@(?<![0-9])' . self::SPACE . '(' . self::DASH . ')' . self::SPACE . '(?![0-9])@u';

        $spaced = preg_match_all($pattern, $content) ?: 0;
        $paired = intdiv($spaced, 2) * 2;
        $index = 0;

        return preg_replace_callback(
            $pattern,
            static function (array $matches) use (&$index, $paired): string {
                $opening = $index < $paired && 0 === $index % 2;
                ++$index;

                return $opening
                    ? ' ' . $matches[1] . Fixer::NO_BREAK_THIN_SPACE
                    : Fixer::NO_BREAK_THIN_SPACE . $matches[1] . ' ';
            },
            $content
        ) ?? $content;
    }

    /**
     * A range opens and closes nothing, so both of its spaces are the same, and neither of them
     * may break: "1964 - 2009" is read as a single value.
     */
    private function bindRanges(string $content): string
    {
        return preg_replace(
            '@([0-9])' . self::SPACE . '(' . self::DASH . ')' . self::SPACE . '(?=[0-9])@u',
            '$1' . Fixer::NO_BREAK_THIN_SPACE . '$2' . Fixer::NO_BREAK_THIN_SPACE,
            $content
        ) ?? $content;
    }
}

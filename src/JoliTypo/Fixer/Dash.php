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
    /** A dash between two numbers, which opens and closes nothing */
    private const string RANGE = 'range';

    /** The first dash of a pair, which binds to what follows */
    private const string OPENING = 'opening';

    /** The second dash of a pair, which binds to what precedes */
    private const string CLOSING = 'closing';

    /** A dash that cannot be told from a plain separator, which binds to what precedes */
    private const string LONE = 'lone';

    /** The spaces that can already surround a dash, line breaks excluded */
    private const string SPACES = '(?:' . Fixer::ALL_SPACES . ')+';

    private const string DASH = '(?:' . Fixer::NDASH . '|' . Fixer::MDASH . ')';

    /**
     * A dash and the spaces around it. The trailing spaces are left out when another dash follows, so
     * that the next dash keeps a space in front of it.
     */
    private const string PATTERN = '@(' . self::SPACES . ')(' . self::DASH . ')(' . self::SPACES . '(?!' . self::DASH . '))?@u';

    /** A pair of dashes spans neither two sentences nor a line break */
    private const string SENTENCE_BOUNDARY = '@([.!?…]+(?:' . Fixer::ALL_SPACES . ')+|\R+)@u';

    /** What closes an incise in place of a space, the end of the sentence included */
    private const string CLOSING_PUNCTUATION = '@^(?:[,;:.!?)\]]|…)@u';

    public function fix(string $content, ?StateBag $stateBag = null): string
    {
        // Convert hyphen between numbers/spaces to en dash (date/number ranges)
        $content = preg_replace('@(?<=[0-9 ]|^)-(?=[0-9 ]|$)@', Fixer::NDASH, $content) ?? $content;

        // Convert double hyphens to em dash
        $content = preg_replace('@ ?-- ?([^-]|$)@s', Fixer::MDASH . '$1', $content) ?? $content;

        $sentences = preg_split(self::SENTENCE_BOUNDARY, $content, -1, \PREG_SPLIT_DELIM_CAPTURE);

        if (false === $sentences) {
            return $content;
        }

        foreach ($sentences as $index => $sentence) {
            $sentences[$index] = $this->bindSpaces($sentence);
        }

        return implode('', $sentences);
    }

    /**
     * Replaces the space on the side a dash binds to with a narrow no-break space, and leaves the other
     * side as the author wrote it. A side without a space stays without one, no space is ever inserted.
     */
    private function bindSpaces(string $sentence): string
    {
        if (!preg_match_all(self::PATTERN, $sentence, $matches, \PREG_OFFSET_CAPTURE | \PREG_SET_ORDER)) {
            return $sentence;
        }

        $kinds = $this->classify($sentence, $matches);
        $index = 0;

        return preg_replace_callback(
            self::PATTERN,
            static function (array $match) use ($kinds, &$index): string {
                $kind = $kinds[$index++];
                $lead = $match[1];
                $dash = $match[2];
                $trail = $match[3] ?? '';
                $bound = Fixer::NO_BREAK_THIN_SPACE;

                if (self::OPENING === $kind) {
                    return $lead . $dash . ('' !== $trail ? $bound : '');
                }

                if (self::RANGE === $kind) {
                    return $bound . $dash . ('' !== $trail ? $bound : '');
                }

                return $bound . $dash . $trail;
            },
            $sentence
        ) ?? $sentence;
    }

    /**
     * Tells what each dash of the sentence is. Two dashes that can open and close one mark an incise;
     * a single one, or three and more, could be anything, so they keep the default spacing.
     *
     * @param array<int, array<int, array{string, int}>> $matches
     *
     * @return array<int, self::RANGE|self::OPENING|self::CLOSING|self::LONE>
     */
    private function classify(string $sentence, array $matches): array
    {
        $kinds = [];
        $pairable = [];

        foreach ($matches as $index => $match) {
            [$full, $offset] = $match[0];
            $trail = $match[3][0] ?? '';
            $after = substr($sentence, $offset + \strlen($full));

            if (1 === preg_match('@[0-9]$@', substr($sentence, 0, $offset))
                && 1 === preg_match('@^[0-9]@', $after)
            ) {
                $kinds[$index] = self::RANGE;

                continue;
            }

            $kinds[$index] = self::LONE;

            // A closing dash is followed by a space, by the punctuation that ends the incise, or by nothing
            if ('' !== $trail || '' === $after || 1 === preg_match(self::CLOSING_PUNCTUATION, $after)) {
                $pairable[] = $index;
            }
        }

        if (2 === \count($pairable)) {
            $kinds[$pairable[0]] = self::OPENING;
            $kinds[$pairable[1]] = self::CLOSING;
        }

        return $kinds;
    }
}

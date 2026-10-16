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

/**
 * Binds an emoji closing a sentence to the word before it, so that it never ends up alone at the beginning of a line.
 *
 * An emoji often closes a sentence like a punctuation mark does ("See you in 2027 🎉"). For the Unicode line breaking
 * algorithm (UAX #14), most emoji behave like ideographs: a line can break before them after a space, and even without
 * any space ("Bravo🎉").
 *
 * Only the emoji followed by punctuation or by the end of the text node are bound, with the emoji following them
 * ("Merci 🙏🎉."): an emoji followed by a word is left alone. The spaces before each emoji are replaced by a
 * NO_BREAK_SPACE, and a WORD_JOINER is inserted where there is no space at all. Are left alone:
 * - a space at the beginning of a text node, it can be the indentation of a block, where a no-break space would be
 *   rendered;
 * - line breaks (#88);
 * - spaces that are already no-break ones (like the narrow no-break space of the Dash fixer).
 *
 * An emoji is what is displayed as one: a flag, a keycap (1️⃣), or a pictograph displayed as an emoji, which is one
 * with the Emoji_Presentation property (🎉, ✅), followed by the U+FE0F variation selector (❤️, ✔️) or by a skin tone
 * (☝🏽). Pictographs displayed as text by default, like ©, ™, ↔ or a bare ❤, are not emoji. The whole sequence is
 * matched: skin tones, ZWJ sequences (👩‍💻) and subdivision flags (🏴󠁧󠁢󠁳󠁣󠁴󠁿) are never split.
 *
 * The Unicode emoji properties require PCRE2 10.40 or higher.
 *
 * @see https://www.unicode.org/reports/tr14/
 * @see https://www.unicode.org/reports/tr51/
 */
class Emoji implements FixerInterface
{
    /**
     * One pictograph of an emoji, with what can follow it: the U+FE0F variation selector, a skin tone, or the tags of a
     * subdivision flag.
     */
    private const string PICTOGRAPH = '\p{Extended_Pictographic}(?:\x{FE0F}|\p{Emoji_Modifier}|[\x{E0020}-\x{E007E}]+\x{E007F})?';

    /**
     * An emoji, see the class documentation: a flag, a keycap, or pictographs joined by ZWJ, the first one being
     * displayed as an emoji.
     */
    private const string EMOJI = '(?:\p{Regional_Indicator}{2}|[#*0-9]\x{FE0F}?\x{20E3}|(?=\p{Emoji_Presentation}|\p{Extended_Pictographic}[\x{FE0F}\p{Emoji_Modifier}])' . self::PICTOGRAPH . '(?:\x{200D}' . self::PICTOGRAPH . ')*)';

    /** What can be found before an emoji: spaces (line breaks excluded), word joiners or nothing at all */
    private const string SEPARATOR = '[' . Fixer::ALL_SPACES_CLASS . Fixer::WORD_JOINER . ']*';

    /** A space a line can break at: any horizontal space, but the no-break ones */
    private const string BREAKING_SPACE = '[^\S\v\x{A0}\x{2007}\x{202F}]';

    public function fix(string $content, ?StateBag $stateBag = null): string
    {
        // Each emoji following a character, with what is before it, when punctuation or the end of the text node comes
        // after it, maybe after other emoji. No line can break after an opening bracket or quote, nor inside a sequence
        // (after a ZWJ), so there is nothing to bind there.
        return preg_replace_callback(
            '@(?<=[^\s\x{200D}' . Fixer::WORD_JOINER . '\p{Ps}\p{Pi}])(' . self::SEPARATOR . ')(' . self::EMOJI . ')(?=(?:' . self::SEPARATOR . self::EMOJI . ')*[' . Fixer::ALL_SPACES_CLASS . ']*(?:\p{P}|$))@u',
            static fn (array $match): string => self::bind($match[1]) . $match[2],
            $content
        ) ?? $content;
    }

    /**
     * Turns what is before an emoji into something a line cannot break at.
     */
    private static function bind(string $separator): string
    {
        if ('' === $separator) {
            return Fixer::WORD_JOINER;
        }

        if (preg_match('@' . self::BREAKING_SPACE . '@u', $separator)) {
            return Fixer::NO_BREAK_SPACE;
        }

        // Only no-break spaces or word joiners, a line can already not break there
        return $separator;
    }
}

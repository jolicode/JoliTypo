<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo\Tests\Fixer;

use JoliTypo\Fixer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class EmojiTest extends TestCase
{
    public function testBindsTheEmojiClosingTheText(): void
    {
        $fixer = new Fixer\Emoji();
        $this->assertInstanceOf('JoliTypo\Fixer\Emoji', $fixer);

        $this->assertSame('Test', $fixer->fix('Test'));
        $this->assertSame('en 2027' . Fixer::NO_BREAK_SPACE . '🎉', $fixer->fix('en 2027 🎉'));
        $this->assertSame('en 2027' . Fixer::NO_BREAK_SPACE . '🎉', $fixer->fix('en 2027   🎉'));
        $this->assertSame('en 2027' . Fixer::NO_BREAK_SPACE . '🎉 ', $fixer->fix('en 2027 🎉 '));
        $this->assertSame('Bravo,' . Fixer::NO_BREAK_SPACE . '🎉', $fixer->fix('Bravo, 🎉'));

        // The emoji following it are bound too
        $this->assertSame('🍎' . Fixer::NO_BREAK_SPACE . '🍌' . Fixer::NO_BREAK_SPACE . '🍒', $fixer->fix('🍎 🍌 🍒'));
        $this->assertSame('Merci' . Fixer::NO_BREAK_SPACE . '🙏' . Fixer::NO_BREAK_SPACE . '🎉', $fixer->fix('Merci 🙏 🎉'));
    }

    public function testBindsTheEmojiFollowedByPunctuation(): void
    {
        $fixer = new Fixer\Emoji();

        $this->assertSame('Merci' . Fixer::NO_BREAK_SPACE . '🙏. La suite', $fixer->fix('Merci 🙏. La suite'));
        $this->assertSame('Merci' . Fixer::NO_BREAK_SPACE . '🙏, la suite', $fixer->fix('Merci 🙏, la suite'));
        $this->assertSame('Thanks' . Fixer::NO_BREAK_SPACE . '🙏! Next', $fixer->fix('Thanks 🙏! Next'));
        $this->assertSame('Bravo' . Fixer::NO_BREAK_SPACE . '🎉) suite', $fixer->fix('Bravo 🎉) suite'));
        $this->assertSame('Bravo' . Fixer::NO_BREAK_SPACE . '🎉… suite', $fixer->fix('Bravo 🎉… suite'));

        // The spaces SpaceBeforePunctuation or SmartQuotes put before the punctuation
        $this->assertSame('Merci' . Fixer::NO_BREAK_SPACE . '🙏' . Fixer::NO_BREAK_THIN_SPACE . '! La suite', $fixer->fix('Merci 🙏' . Fixer::NO_BREAK_THIN_SPACE . '! La suite'));
        $this->assertSame('«' . Fixer::NO_BREAK_SPACE . 'bravo' . Fixer::NO_BREAK_SPACE . '🎉' . Fixer::NO_BREAK_SPACE . '» hier', $fixer->fix('«' . Fixer::NO_BREAK_SPACE . 'bravo 🎉' . Fixer::NO_BREAK_SPACE . '» hier'));
    }

    public function testLeavesTheEmojiFollowedByAWordAlone(): void
    {
        $fixer = new Fixer\Emoji();

        $this->assertSame('On a gagné 🎉 Rendez-vous', $fixer->fix('On a gagné 🎉 Rendez-vous'));
        $this->assertSame('Super 🎉 on a gagné.', $fixer->fix('Super 🎉 on a gagné.'));
        $this->assertSame('Super🎉on a gagné', $fixer->fix('Super🎉on a gagné'));

        // Nothing to bind an emoji starting the text to
        $this->assertSame('🎉 Bravo', $fixer->fix('🎉 Bravo'));
        $this->assertSame('🎉', $fixer->fix('🎉'));
    }

    public function testInsertsAWordJoinerWhereThereIsNoSpace(): void
    {
        $fixer = new Fixer\Emoji();

        // A line can break before an emoji even without a space, as before an ideograph
        $this->assertSame('Bravo' . Fixer::WORD_JOINER . '🎉', $fixer->fix('Bravo🎉'));
        $this->assertSame('Merci!' . Fixer::WORD_JOINER . '🙏', $fixer->fix('Merci!🙏'));
        $this->assertSame('Merci' . Fixer::NO_BREAK_SPACE . '🙏' . Fixer::WORD_JOINER . '🎉.', $fixer->fix('Merci 🙏🎉.'));

        // No line can break after an opening bracket or quote
        $this->assertSame('(🎉)', $fixer->fix('(🎉)'));
        $this->assertSame('“🎉”', $fixer->fix('“🎉”'));
    }

    #[DataProvider('provideEmoji')]
    public function testBindsTheWholeSequence(string $emoji): void
    {
        $fixer = new Fixer\Emoji();

        $this->assertSame('Bravo' . Fixer::NO_BREAK_SPACE . $emoji, $fixer->fix('Bravo ' . $emoji));
        $this->assertSame('Bravo' . Fixer::WORD_JOINER . $emoji, $fixer->fix('Bravo' . $emoji));
        $this->assertSame('Bravo' . Fixer::NO_BREAK_SPACE . $emoji . '. Suite', $fixer->fix('Bravo ' . $emoji . '. Suite'));
        $this->assertSame('Bravo' . Fixer::NO_BREAK_SPACE . $emoji . Fixer::WORD_JOINER . $emoji, $fixer->fix('Bravo ' . $emoji . $emoji));
        $this->assertSame('Bravo ' . $emoji . ' suite', $fixer->fix('Bravo ' . $emoji . ' suite'));
        $this->assertSame($emoji . ' Bravo', $fixer->fix($emoji . ' Bravo'));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEmoji(): iterable
    {
        yield 'single code point' => ['🙏'];
        yield 'Emoji_Presentation pictograph' => ["\u{2705}"]; // ✅
        yield 'text pictograph with U+FE0F' => ["\u{2764}\u{FE0F}"]; // ❤️
        yield 'skin tone' => ["\u{1F44D}\u{1F3FD}"]; // 👍🏽
        yield 'text pictograph with a skin tone' => ["\u{261D}\u{1F3FD}"]; // ☝🏽
        yield 'ZWJ sequence' => ["\u{1F469}\u{200D}\u{1F4BB}"]; // 👩‍💻
        yield 'ZWJ sequence with U+FE0F' => ["\u{1F3F3}\u{FE0F}\u{200D}\u{1F308}"]; // 🏳️‍🌈
        yield 'ZWJ sequence ending with U+FE0F' => ["\u{1F937}\u{200D}\u{2640}\u{FE0F}"]; // 🤷‍♀️
        yield 'ZWJ sequence with a skin tone' => ["\u{1F9D1}\u{1F3FD}\u{200D}\u{1F4BB}"]; // 🧑🏽‍💻
        yield 'ZWJ sequence with two skin tones' => ["\u{1FAF1}\u{1F3FB}\u{200D}\u{1FAF2}\u{1F3FF}"]; // 🫱🏻‍🫲🏿
        yield 'family' => ["\u{1F468}\u{200D}\u{1F469}\u{200D}\u{1F467}\u{200D}\u{1F466}"]; // 👨‍👩‍👧‍👦
        yield 'flag' => ["\u{1F1EB}\u{1F1F7}"]; // 🇫🇷
        yield 'subdivision flag' => ["\u{1F3F4}\u{E0067}\u{E0062}\u{E0073}\u{E0063}\u{E0074}\u{E007F}"]; // 🏴󠁧󠁢󠁳󠁣󠁴󠁿
        yield 'digit keycap' => ["1\u{FE0F}\u{20E3}"]; // 1️⃣
        yield 'hash keycap' => ["#\u{FE0F}\u{20E3}"]; // #️⃣
    }

    public function testLeavesTextPictographsAlone(): void
    {
        $fixer = new Fixer\Emoji();

        // Displayed as text by default, without the U+FE0F variation selector
        $this->assertSame("I love PHP \u{2764}", $fixer->fix("I love PHP \u{2764}"));
        $this->assertSame('Protip ©', $fixer->fix('Protip ©'));
        $this->assertSame('Acme ®', $fixer->fix('Acme ®'));
        $this->assertSame('Acme ™', $fixer->fix('Acme ™'));
        $this->assertSame('Paris ↔', $fixer->fix('Paris ↔'));

        // Digits, # and * have the Emoji property, but are emoji only in a keycap
        $this->assertSame('Step 1', $fixer->fix('Step 1'));
        $this->assertSame('Room #', $fixer->fix('Room #'));
        $this->assertSame('Note *', $fixer->fix('Note *'));

        // A sequence starting with a text pictograph is never split
        $this->assertSame("Love \u{2764}\u{200D}\u{1F525}.", $fixer->fix("Love \u{2764}\u{200D}\u{1F525}."));
    }

    public function testReplacesOnlyTheSpacesBetweenACharacterAndAnEmoji(): void
    {
        $fixer = new Fixer\Emoji();

        // Tabs are spaces, but line breaks are not (#88)
        $this->assertSame('en 2027' . Fixer::NO_BREAK_SPACE . '🎉', $fixer->fix("en 2027\t🎉"));
        $this->assertSame("en 2027\n🎉", $fixer->fix("en 2027\n🎉"));

        // Spaces starting a line or a text node can be indentation, where a no-break space would be rendered
        $this->assertSame("Bravo\n    🎉", $fixer->fix("Bravo\n    🎉"));
        $this->assertSame(' 🎉', $fixer->fix(' 🎉'));
    }

    public function testKeepsNoBreakSpacesAndWordJoiners(): void
    {
        $fixer = new Fixer\Emoji();

        $this->assertSame('Bravo' . Fixer::NO_BREAK_SPACE . '🎉', $fixer->fix('Bravo' . Fixer::NO_BREAK_SPACE . '🎉'));
        $this->assertSame('Bravo' . Fixer::WORD_JOINER . '🎉', $fixer->fix('Bravo' . Fixer::WORD_JOINER . '🎉'));
        $this->assertSame('Merci' . Fixer::NO_BREAK_SPACE . '🙏' . Fixer::WORD_JOINER . '🎉', $fixer->fix('Merci' . Fixer::NO_BREAK_SPACE . '🙏' . Fixer::WORD_JOINER . '🎉'));
        // The narrow no-break space put by Dash or SpaceBeforePunctuation is kept
        $this->assertSame('Style –' . Fixer::NO_BREAK_THIN_SPACE . '🎉', $fixer->fix('Style –' . Fixer::NO_BREAK_THIN_SPACE . '🎉'));
        // A line can still break at a space mixed with no-break ones
        $this->assertSame('Bravo' . Fixer::NO_BREAK_SPACE . '🎉', $fixer->fix('Bravo' . Fixer::NO_BREAK_SPACE . ' 🎉'));
        $this->assertSame('Bravo' . Fixer::NO_BREAK_SPACE . '🎉', $fixer->fix('Bravo' . Fixer::WORD_JOINER . ' 🎉'));
    }

    public function testKeepsInvalidUtf8Untouched(): void
    {
        $fixer = new Fixer\Emoji();

        $this->assertSame("Caf\xE9 🎉", $fixer->fix("Caf\xE9 🎉"));
    }
}

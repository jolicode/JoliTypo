<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo\Fixer;

use JoliTypo\Exception\BadFixerConfigurationException;
use JoliTypo\Fixer;
use JoliTypo\FixerInterface;
use JoliTypo\LocaleAwareFixerInterface;
use JoliTypo\LocaleConfig;
use JoliTypo\StateBag;

/**
 * Replaces straight double quotes with typographic quotation marks, and pairs of
 * straight single quotes with the nested (second-level) quotation marks.
 *
 * The style of quotation marks depends on the locale:
 * - French: « … » (guillemets with non-breaking spaces), “…” inside
 * - German: „…“ (low-high double quotes), ‚…‘ inside
 * - English: “…” (curly double quotes), ‘…’ inside
 * - Finnish/Swedish: ”…” (same closing quote on both sides), ’…’ inside
 * - And many more...
 *
 * Apostrophes (I'm, l'univers) are left untouched, CurlyQuote takes care of them.
 * This fixer must run BEFORE CurlyQuote, otherwise a closing single quote following
 * a letter is mistaken for an apostrophe.
 *
 * @see LocaleConfig::QUOTE_STYLES_BY_LOCALE for the full list
 * @see LocaleConfig::NESTED_QUOTE_STYLES_BY_LOCALE for the nested quotation marks
 */
class SmartQuotes extends BaseOpenClosePair implements FixerInterface, LocaleAwareFixerInterface
{
    /**
     * Spaces, including the non-breaking ones added inside the double quotes.
     */
    private const string SPACES = '\s\x{00A0}\x{202F}';

    /**
     * Double quotation marks, dumb or smart, which can surround a nested quotation.
     */
    private const string DOUBLE_QUOTES = '"' . Fixer::LDQUO . Fixer::RDQUO . Fixer::BDQUO . Fixer::LAQUO . Fixer::RAQUO;

    /**
     * Characters allowed before an opening single quote: spaces, opening brackets, double quotes.
     */
    private const string BEFORE_SINGLE_OPENING_CHARS = self::SPACES . '(\[' . self::DOUBLE_QUOTES;

    /**
     * Characters allowed after a closing single quote: spaces, punctuation, closing brackets, double quotes.
     */
    private const string AFTER_SINGLE_CLOSING_CHARS = self::SPACES . '.,;:!?)\]' . self::DOUBLE_QUOTES;

    /**
     * An opening single quote: at the beginning of the text, or preceded by one of BEFORE_SINGLE_OPENING_CHARS.
     * The preceding character is captured so that the replacement can restore it.
     */
    private const string SINGLE_OPENING = '(^|[' . self::BEFORE_SINGLE_OPENING_CHARS . '])\'';

    /**
     * A closing single quote: at the end of the text, or followed by one of AFTER_SINGLE_CLOSING_CHARS.
     */
    private const string SINGLE_CLOSING = '\'(?=[' . self::AFTER_SINGLE_CLOSING_CHARS . ']|$)';

    /**
     * One character of a nested quotation: anything but an opening or a closing single quote.
     * A single quote inside a word (I'm) is neither, so apostrophes are allowed in the content.
     */
    private const string SINGLE_CONTENT = '(?:\'(?![' . self::AFTER_SINGLE_CLOSING_CHARS . ']|$)|[' . self::BEFORE_SINGLE_OPENING_CHARS . '](?!\')|[^' . self::BEFORE_SINGLE_OPENING_CHARS . '\'])';

    protected string $opening = '';

    protected string $openingSuffix = '';

    protected string $closing = '';

    protected string $closingPrefix = '';

    protected string $nestedOpening = '';

    protected string $nestedClosing = '';

    public function __construct(string $locale)
    {
        $this->setLocale($locale);
    }

    public function fix(string $content, ?StateBag $stateBag = null): string
    {
        if (!$this->opening || !$this->closing) {
            throw new BadFixerConfigurationException();
        }

        $content = $this->fixDoubleQuotes($content, $stateBag);

        // Without nested quotation marks (custom marks on an unknown locale), single quotes are left as they are
        if ($this->nestedOpening && $this->nestedClosing) {
            $content = $this->fixSingleQuotes($content, $stateBag);
        }

        return $content;
    }

    /**
     * Set locale and configure quotation marks accordingly.
     */
    public function setLocale(string $locale): void
    {
        $style = LocaleConfig::getQuotationStyle($locale);

        if (null !== $style) {
            $this->opening = $style['opening'];
            $this->openingSuffix = $style['openingSuffix'];
            $this->closing = $style['closing'];
            $this->closingPrefix = $style['closingPrefix'];
        }

        $nestedStyle = LocaleConfig::getNestedQuotationStyle($locale);

        if (null !== $nestedStyle) {
            $this->nestedOpening = $nestedStyle['opening'] . $nestedStyle['openingSuffix'];
            $this->nestedClosing = $nestedStyle['closingPrefix'] . $nestedStyle['closing'];
        }
    }

    public function setOpening(string $opening): void
    {
        $this->opening = $opening;
    }

    public function setOpeningSuffix(string $openingSuffix): void
    {
        $this->openingSuffix = $openingSuffix;
    }

    public function setClosing(string $closing): void
    {
        $this->closing = $closing;
    }

    public function setClosingPrefix(string $closingPrefix): void
    {
        $this->closingPrefix = $closingPrefix;
    }

    /**
     * Set the opening mark of the nested quotations, e.g. › for the German »…« style.
     */
    public function setNestedOpening(string $nestedOpening): void
    {
        $this->nestedOpening = $nestedOpening;
    }

    /**
     * Set the closing mark of the nested quotations, e.g. ‹ for the German »…« style.
     */
    public function setNestedClosing(string $nestedClosing): void
    {
        $this->nestedClosing = $nestedClosing;
    }

    private function fixDoubleQuotes(string $content, ?StateBag $stateBag): string
    {
        // Fix complex siblings cases
        if ($stateBag) {
            $content = $this->fixViaState(
                $content,
                $stateBag,
                'SmartQuotesOpenSolo',
                '@(^|\s|\()"([^"]*)$@imu',
                // Same strategy as for the simple cases below, see the comment there
                '@(?|(^|[^"]*)"(?=[^"]*(?:$|[\s(]"))|(^|(?:[^"]|(?<=\d)")*?)(?<!\d)"|(^|[^"]+)")@imu',
                $this->opening . $this->openingSuffix,
                $this->closingPrefix . $this->closing
            );
        }

        $replacement = '$1' . $this->opening . $this->openingSuffix . '$2' . $this->closingPrefix . $this->closing;

        // Fix simple cases. A double quote preceded by a digit may be an inch or a second mark (5'6", 27")
        // rather than a closing quote, so the closing quote is searched in three passes:
        // 1. the nearest double quote, when no other double quote stands between it and the next opening quote or the end;
        // 2. the nearest double quote not preceded by a digit;
        // 3. the nearest double quote, whatever precedes it.
        $content = preg_replace('@(^|\s|\()"([^"]+)"(?=[^"]*(?:$|[\s(]"))@imu', $replacement, $content) ?? $content;
        $content = preg_replace('@(^|\s|\()"((?:[^"]|(?<=\d)")+?)(?<!\d)"@imu', $replacement, $content) ?? $content;

        return preg_replace('@(^|\s|\()"([^"]+)"@imu', $replacement, $content) ?? $content;
    }

    /**
     * Convert pairs of single quotes to the nested quotation marks. Runs after the double quotes,
     * so that the smart double quotes are valid neighbours of the single quotes.
     */
    private function fixSingleQuotes(string $content, ?StateBag $stateBag): string
    {
        // Fix complex siblings cases: the opening quote is in this node, the closing one in a later sibling node
        if ($stateBag) {
            $content = $this->fixViaState(
                $content,
                $stateBag,
                'SmartQuotesNestedOpenSolo',
                '@' . self::SINGLE_OPENING . '(' . self::SINGLE_CONTENT . '*)$@u',
                '@^(' . self::SINGLE_CONTENT . '*)' . self::SINGLE_CLOSING . '@u',
                $this->nestedOpening,
                $this->nestedClosing
            );
        }

        // Fix simple cases: both quotes are in this node
        return preg_replace(
            '@' . self::SINGLE_OPENING . '(' . self::SINGLE_CONTENT . '+)' . self::SINGLE_CLOSING . '@u',
            '${1}' . $this->nestedOpening . '${2}' . $this->nestedClosing,
            $content
        ) ?? $content;
    }
}

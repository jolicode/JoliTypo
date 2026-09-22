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
 * Converts pairs of straight single quotes ('…') to the nested (second-level)
 * quotation marks of the locale, e.g. ‘…’ in English, ‚…‘ in German or “…” in French.
 *
 * Apostrophes (I'm, l'univers) are left untouched, CurlyQuote takes care of them.
 * This fixer must run BEFORE CurlyQuote, otherwise a closing quote following a
 * letter is mistaken for an apostrophe.
 *
 * @see LocaleConfig::NESTED_QUOTE_STYLES_BY_LOCALE
 */
class SmartSingleQuotes extends BaseOpenClosePair implements FixerInterface, LocaleAwareFixerInterface
{
    /**
     * Spaces, including the non-breaking ones SmartQuotes may have added inside the double quotes.
     */
    private const SPACES = '\s\x{00A0}\x{202F}';

    /**
     * Double quotation marks, dumb or smart, which can surround a nested quotation.
     */
    private const DOUBLE_QUOTES = '"' . Fixer::LDQUO . Fixer::RDQUO . Fixer::BDQUO . Fixer::LAQUO . Fixer::RAQUO;

    /**
     * Characters allowed before an opening quote: spaces, opening brackets, double quotes.
     */
    private const BEFORE_OPENING_CHARS = self::SPACES . '(\[' . self::DOUBLE_QUOTES;

    /**
     * Characters allowed after a closing quote: spaces, punctuation, closing brackets, double quotes.
     */
    private const AFTER_CLOSING_CHARS = self::SPACES . '.,;:!?)\]' . self::DOUBLE_QUOTES;

    /**
     * An opening quote: a single quote at the beginning of the text, or preceded by one of BEFORE_OPENING_CHARS.
     * The preceding character is captured so that the replacement can restore it.
     */
    private const OPENING = '(^|[' . self::BEFORE_OPENING_CHARS . '])\'';

    /**
     * A closing quote: a single quote at the end of the text, or followed by one of AFTER_CLOSING_CHARS.
     */
    private const CLOSING = '\'(?=[' . self::AFTER_CLOSING_CHARS . ']|$)';

    /**
     * One character of the quoted content: anything but an opening or a closing quote.
     * A single quote inside a word (I'm) is neither, so apostrophes are allowed in the content.
     */
    private const CONTENT = '(?:\'(?![' . self::AFTER_CLOSING_CHARS . ']|$)|[' . self::BEFORE_OPENING_CHARS . '](?!\')|[^' . self::BEFORE_OPENING_CHARS . '\'])';

    protected string $opening = '';

    protected string $openingSuffix = '';

    protected string $closing = '';

    protected string $closingPrefix = '';

    public function __construct(string $locale)
    {
        $this->setLocale($locale);
    }

    public function fix(string $content, ?StateBag $stateBag = null): string
    {
        if (!$this->opening || !$this->closing) {
            throw new BadFixerConfigurationException();
        }

        // Fix complex siblings cases: the opening quote is in this node, the closing one in a later sibling node
        if ($stateBag) {
            $content = $this->fixViaState(
                $content,
                $stateBag,
                'SmartSingleQuotesOpenSolo',
                '@' . self::OPENING . '(' . self::CONTENT . '*)$@u',
                '@^(' . self::CONTENT . '*)' . self::CLOSING . '@u',
                $this->opening . $this->openingSuffix,
                $this->closingPrefix . $this->closing
            );
        }

        // Fix simple cases: both quotes are in this node
        return preg_replace(
            '@' . self::OPENING . '(' . self::CONTENT . '+)' . self::CLOSING . '@u',
            '${1}' . $this->opening . $this->openingSuffix . '${2}' . $this->closingPrefix . $this->closing,
            $content
        ) ?? $content;
    }

    /**
     * Set locale and configure the nested quotation marks accordingly.
     */
    public function setLocale(string $locale): void
    {
        $style = LocaleConfig::getNestedQuotationStyle($locale);

        if (null !== $style) {
            $this->opening = $style['opening'];
            $this->openingSuffix = $style['openingSuffix'];
            $this->closing = $style['closing'];
            $this->closingPrefix = $style['closingPrefix'];
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
}

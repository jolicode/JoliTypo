<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo\Fixer;

use JoliTypo\Exception\BadFixerConfigurationException;
use JoliTypo\FixerInterface;
use JoliTypo\LocaleAwareFixerInterface;
use JoliTypo\LocaleConfig;
use JoliTypo\StateBag;

/**
 * Replaces straight double quotes with typographic quotation marks.
 *
 * The style of quotation marks depends on the locale:
 * - French: « … » (guillemets with non-breaking spaces)
 * - German: „…" (low-high double quotes)
 * - English: "…" (curly double quotes)
 * - Finnish/Swedish: "…" (same closing quote on both sides)
 * - And many more...
 *
 * @see LocaleConfig::QUOTE_STYLES_BY_LOCALE for the full list
 */
class SmartQuotes extends BaseOpenClosePair implements FixerInterface, LocaleAwareFixerInterface
{
    protected string $opening = '';

    protected string $openingSuffix = '';

    protected string $closing = '';

    protected string $closingPrefix = '';

    public function __construct(string $locale)
    {
        $this->setLocale($locale);
    }

    public function fix(string $content, ?StateBag $stateBag = null)
    {
        if (!$this->opening || !$this->closing) {
            throw new BadFixerConfigurationException();
        }

        // Fix complex siblings cases
        if ($stateBag) {
            $content = $this->fixViaState(
                $content,
                $stateBag,
                'SmartQuotesOpenSolo',
                '@(^|\s|\()"([^"]*)$@im',
                // Same strategy as for the simple cases below, see the comment there
                '@(?|(^|[^"]*)"(?=[^"]*(?:$|[\s(]"))|(^|(?:[^"]|(?<=\d)")*?)(?<!\d)"|(^|[^"]+)")@im',
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
        $content = preg_replace('@(^|\s|\()"([^"]+)"(?=[^"]*(?:$|[\s(]"))@im', $replacement, $content) ?? $content;
        $content = preg_replace('@(^|\s|\()"((?:[^"]|(?<=\d)")+?)(?<!\d)"@im', $replacement, $content) ?? $content;

        return preg_replace('@(^|\s|\()"([^"]+)"@im', $replacement, $content) ?? $content;
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

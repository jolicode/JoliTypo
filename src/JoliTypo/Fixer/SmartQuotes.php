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
use JoliTypo\StateBag;

class SmartQuotes extends BaseOpenClosePair implements FixerInterface, LocaleAwareFixerInterface
{
    protected string $opening       = '';
    protected string $openingSuffix = '';
    protected string $closing       = '';
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

        // Fix complex siblings cases
        if ($stateBag) {
            $content = $this->fixViaState(
                $content,
                $stateBag,
                'SmartQuotesOpenSolo',
                '@(^|\s|\()"([^"]*)$@im',
                '@(^|[^"]+)"@im',
                $this->opening . $this->openingSuffix,
                $this->closingPrefix . $this->closing
            );
        }

        // Fix simple cases
        return preg_replace(
            '@(^|\s|\()"([^"]+)"@im',
            '$1' . $this->opening . $this->openingSuffix . '$2' . $this->closingPrefix . $this->closing,
            $content
        );
    }

    /**
     * Default configuration for supported lang.
     */
    public function setLocale(string $locale): void
    {
        // Handle from locale + country
        switch (strtolower($locale)) {
            // “…”
            case 'pt-br':
                $this->opening = Fixer::LDQUO;
                $this->openingSuffix = '';
                $this->closing = Fixer::RDQUO;
                $this->closingPrefix = '';

                return;
                // «…»
            case 'de-ch':
                $this->opening = Fixer::LAQUO;
                $this->openingSuffix = '';
                $this->closing = Fixer::RAQUO;
                $this->closingPrefix = '';

                return;
        }

        // Handle from locale only
        $short = Fixer::getLanguageFromLocale($locale);

        switch ($short) {
            // « … »
            case 'fr':
                $this->opening = Fixer::LAQUO;
                $this->openingSuffix = Fixer::NO_BREAK_SPACE;
                $this->closing = Fixer::RAQUO;
                $this->closingPrefix = Fixer::NO_BREAK_SPACE;

                break;
                // «…»
            case 'hy':
            case 'az':
            case 'hz':
            case 'eu':
            case 'be':
            case 'ca':
            case 'el':
            case 'it':
            case 'no':
            case 'fa':
            case 'lv':
            case 'pt':
            case 'ru':
            case 'es':
            case 'uk':
                $this->opening = Fixer::LAQUO;
                $this->openingSuffix = '';
                $this->closing = Fixer::RAQUO;
                $this->closingPrefix = '';

                break;
                // „…“
            case 'de':
            case 'ka':
            case 'cs':
            case 'et':
            case 'is':
            case 'lt':
            case 'mk':
            case 'ro':
            case 'sk':
            case 'sl':
            case 'wen':
                $this->opening = Fixer::BDQUO;
                $this->openingSuffix = '';
                $this->closing = Fixer::LDQUO;
                $this->closingPrefix = '';

                break;
                // “…”
            case 'en':
            case 'us':
            case 'gb':
            case 'af':
            case 'ar':
            case 'eo':
            case 'id':
            case 'ga':
            case 'ko':
            case 'br':
            case 'th':
            case 'tr':
            case 'vi':
                $this->opening = Fixer::LDQUO;
                $this->openingSuffix = '';
                $this->closing = Fixer::RDQUO;
                $this->closingPrefix = '';

                break;
                // ”…”
            case 'fi':
            case 'sv':
            case 'bs':
                $this->opening = Fixer::RDQUO;
                $this->openingSuffix = '';
                $this->closing = Fixer::RDQUO;
                $this->closingPrefix = '';

                break;
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

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
use JoliTypo\LocaleAwareFixerInterface;
use JoliTypo\StateBag;
use Org\Heigl\Hyphenator\Hyphenator;

class Hyphen implements FixerInterface, LocaleAwareFixerInterface
{
    private Hyphenator $hyphenator;

    private array $supportedLocales = [
        'af_ZA',
        'ca',
        'da_DK',
        'de_AT',
        'de_CH',
        'de_DE',
        'en_GB',
        'en_UK',
        'et_EE',
        'fr',
        'hr_HR',
        'hu_HU',
        'it_IT',
        'lt_LT',
        'nb_NO',
        'nn_NO',
        'nl_NL',
        'pl_PL',
        'pt_BR',
        'ro_RO',
        'ru_RU',
        'sk_SK',
        'sl_SI',
        'sr',
        'zu_ZA',
    ];

    /**
     * @param int $leftMin  Minimum number of characters kept before the first hyphenation point of a word
     * @param int $rightMin Minimum number of characters kept after the last hyphenation point of a word
     * @param int $wordMin  Minimum length of a word (in characters) to be hyphenated
     */
    public function __construct(
        string $locale,
        private readonly int $leftMin = 4,
        private readonly int $rightMin = 3,
        private readonly int $wordMin = 6,
    ) {
        $this->setLocale($locale);
    }

    public function setLocale(string $locale): void
    {
        $this->hyphenator = Hyphenator::factory(null, $this->fixLocale($locale));
        $this->setOptions();
    }

    /**
     * @return string
     */
    public function fix(string $content, ?StateBag $stateBag = null)
    {
        return $this->hyphenator->hyphenate($content);
    }

    protected function setOptions(): void
    {
        $options = $this->hyphenator->getOptions();
        $options->setHyphen(Fixer::SHY);
        $options->setLeftMin($this->leftMin);
        $options->setRightMin($this->rightMin);
        $options->setMinWordLength($this->wordMin);
    }

    /**
     * Transform fr_FR to fr to fit the list of supported locales.
     *
     * @return string
     */
    protected function fixLocale(string $locale)
    {
        if (\in_array($locale, $this->supportedLocales)) {
            return $locale;
        }

        if (($short = Fixer::getLanguageFromLocale($locale)) !== $locale) {
            if (\in_array($short, $this->supportedLocales)) {
                return $short;
            }
        }

        // If no better locale found...
        return $locale;
    }
}

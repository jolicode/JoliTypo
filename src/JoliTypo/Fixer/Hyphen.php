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
    private const array SUPPORTED_LOCALES = [
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

    private Hyphenator $hyphenator;

    public function __construct(string $locale)
    {
        $this->setLocale($locale);
    }

    public function setLocale(string $locale): void
    {
        $this->hyphenator = Hyphenator::factory(null, $this->fixLocale($locale));
        $this->setOptions();
    }

    public function fix(string $content, ?StateBag $stateBag = null): string
    {
        return $this->hyphenator->hyphenate($content);
    }

    protected function setOptions(): void
    {
        $this->hyphenator->getOptions()->setHyphen(Fixer::SHY);
        $this->hyphenator->getOptions()->setLeftMin(4);
        $this->hyphenator->getOptions()->setRightMin(3);
    }

    /**
     * Transform fr_FR to fr to fit the list of supported locales.
     */
    protected function fixLocale(string $locale): string
    {
        if (\in_array($locale, self::SUPPORTED_LOCALES)) {
            return $locale;
        }

        if (($short = Fixer::getLanguageFromLocale($locale)) !== $locale) {
            if (\in_array($short, self::SUPPORTED_LOCALES)) {
                return $short;
            }
        }

        // If no better locale found...
        return $locale;
    }
}

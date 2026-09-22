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
     * Words already containing a soft hyphen are considered hyphenated and are left untouched:
     * the hyphenator does not know about soft hyphens and would add new ones next to the
     * existing ones every time already fixed content is fixed again.
     *
     * @see https://github.com/jolicode/JoliTypo/issues/57
     */
    public function fix(string $content, ?StateBag $stateBag = null): string
    {
        // Same separators as the hyphenator's own whitespace tokenizer
        return preg_replace_callback(
            '/[^\s\x{00A0}\x{202F}]+/u',
            fn (array $matches): string => str_contains($matches[0], Fixer::SHY) ? $matches[0] : $this->hyphenate($matches[0]),
            $content
        ) ?? $content;
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

    private function hyphenate(string $word): string
    {
        $hyphenated = $this->hyphenator->hyphenate($word);

        // The hyphenator is documented as returning an array with some filters, but the default one always returns a string
        return \is_string($hyphenated) ? $hyphenated : $word;
    }
}

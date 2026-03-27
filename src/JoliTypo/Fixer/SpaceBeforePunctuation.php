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
use JoliTypo\LocaleConfig;
use JoliTypo\StateBag;

/**
 * Manages spacing before punctuation marks according to locale rules.
 *
 * Supported locales:
 *
 * French (fr_FR, fr_BE, fr_CH):
 * - NO_BREAK_SPACE before :
 * - NO_BREAK_THIN_SPACE before ; ! ?
 * - NO_BREAK_SPACE inside « »
 *
 * Swiss German (de_CH):
 * - NO_BREAK_THIN_SPACE inside « » (Swiss German uses French-style guillemets)
 *
 * Canadian French (fr_CA), English, German, Spanish, Italian, and other locales:
 * - Removes any space before : ; ! ?
 *
 * @see https://fr.wikipedia.org/wiki/Ponctuation#Espaces_et_ponctuation
 * @see https://type.today/en/journal/spaces
 * @see https://www.mancko.com/typography-punctuation/en/
 * @see "Abrégé du code typographique à l'usage de la presse", ISBN: 978-2351130667
 */
class SpaceBeforePunctuation implements FixerInterface, LocaleAwareFixerInterface
{
    private string $locale = 'en_GB';

    private string $currentRule = LocaleConfig::SPACING_RULE_NONE;

    public function __construct(?string $locale = null)
    {
        if (null !== $locale) {
            $this->setLocale($locale);
        }
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
        $this->currentRule = LocaleConfig::getSpacingRule($locale);
    }

    public function fix(string $content, ?StateBag $stateBag = null): string
    {
        return match ($this->currentRule) {
            LocaleConfig::SPACING_RULE_FRENCH => $this->applyFrenchRules($content),
            LocaleConfig::SPACING_RULE_SWISS_GERMAN => $this->applySwissGermanRules($content),
            default => $this->removeSpacesBeforePunctuation($content),
        };
    }

    private function applyFrenchRules(string $content): string
    {
        // NO_BREAK_SPACE before colon (only when there's already a space)
        $content = preg_replace('@[' . Fixer::ALL_SPACES . ']+(:)@mu', Fixer::NO_BREAK_SPACE . '$1', $content);

        // NO_BREAK_THIN_SPACE before ; ! ?
        $content = preg_replace('@[' . Fixer::ALL_SPACES . ']+([;!\?])@mu', Fixer::NO_BREAK_THIN_SPACE . '$1', $content);

        // Handle French guillemets « »
        $content = preg_replace('@' . Fixer::LAQUO . '[' . Fixer::ALL_SPACES . ']?@mu', Fixer::LAQUO . Fixer::NO_BREAK_SPACE, $content);

        return preg_replace('@[' . Fixer::ALL_SPACES . ']?' . Fixer::RAQUO . '@mu', Fixer::NO_BREAK_SPACE . Fixer::RAQUO, $content);
    }

    private function applySwissGermanRules(string $content): string
    {
        // Swiss German uses French-style guillemets « » with thin non-breaking spaces
        $content = preg_replace('@' . Fixer::LAQUO . '[' . Fixer::ALL_SPACES . ']?@mu', Fixer::LAQUO . Fixer::NO_BREAK_THIN_SPACE, $content);
        $content = preg_replace('@[' . Fixer::ALL_SPACES . ']?' . Fixer::RAQUO . '@mu', Fixer::NO_BREAK_THIN_SPACE . Fixer::RAQUO, $content);

        // But still remove spaces before punctuation (like German)
        return $this->removeSpacesBeforePunctuation($content);
    }

    private function removeSpacesBeforePunctuation(string $content): string
    {
        // Remove spaces before : ; ! ? (but not when it's part of URL, time, IPv6, etc.)
        // Only remove when there's a space before the punctuation
        $content = preg_replace('@([^\s:])[ ]+(:)(?![/\d])@mu', '$1$2', $content);

        return preg_replace('@([^\s])[ ]+([;!\?])@mu', '$1$2', $content);
    }
}

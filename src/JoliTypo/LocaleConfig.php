<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo;

/**
 * Centralized locale configuration for typography rules.
 *
 * This class holds all locale-specific configurations used by various fixers.
 * Adding support for a new language can be done by adding its configuration here.
 */
final class LocaleConfig
{
    /**
     * Rule types for spacing before punctuation.
     */
    public const SPACING_RULE_FRENCH = 'french';
    public const SPACING_RULE_SWISS_GERMAN = 'swiss_german';
    public const SPACING_RULE_NONE = 'none';

    /**
     * Quotation mark styles.
     *
     * QUOTE_STYLE_FRENCH: « … » (guillemets with non-breaking spaces)
     * QUOTE_STYLE_GUILLEMETS: «…» (guillemets without spaces)
     * QUOTE_STYLE_GERMAN: „…" (low-high double quotes)
     * QUOTE_STYLE_ENGLISH: "…" (curly double quotes)
     * QUOTE_STYLE_FINNISH: "…" (same closing quote on both sides)
     */
    public const QUOTE_STYLE_FRENCH = 'french';
    public const QUOTE_STYLE_GUILLEMETS = 'guillemets';
    public const QUOTE_STYLE_GERMAN = 'german';
    public const QUOTE_STYLE_ENGLISH = 'english';
    public const QUOTE_STYLE_FINNISH = 'finnish';

    /**
     * Quotation styles by locale.
     *
     * Maps locale/language codes to their quotation mark style.
     */
    public const QUOTE_STYLES_BY_LOCALE = [
        // =====================================================================
        // French style: « … » (with non-breaking spaces)
        // =====================================================================
        'fr' => self::QUOTE_STYLE_FRENCH,

        // =====================================================================
        // Guillemets without spaces: «…»
        // =====================================================================
        'hy' => self::QUOTE_STYLE_GUILLEMETS, // Armenian
        'az' => self::QUOTE_STYLE_GUILLEMETS, // Azerbaijani
        'eu' => self::QUOTE_STYLE_GUILLEMETS, // Basque
        'be' => self::QUOTE_STYLE_GUILLEMETS, // Belarusian
        'ca' => self::QUOTE_STYLE_GUILLEMETS, // Catalan
        'el' => self::QUOTE_STYLE_GUILLEMETS, // Greek
        'it' => self::QUOTE_STYLE_GUILLEMETS, // Italian
        'no' => self::QUOTE_STYLE_GUILLEMETS, // Norwegian
        'nb' => self::QUOTE_STYLE_GUILLEMETS, // Norwegian Bokmål
        'nn' => self::QUOTE_STYLE_GUILLEMETS, // Norwegian Nynorsk
        'fa' => self::QUOTE_STYLE_GUILLEMETS, // Persian
        'lv' => self::QUOTE_STYLE_GUILLEMETS, // Latvian
        'pt' => self::QUOTE_STYLE_GUILLEMETS, // Portuguese
        'ru' => self::QUOTE_STYLE_GUILLEMETS, // Russian
        'es' => self::QUOTE_STYLE_GUILLEMETS, // Spanish
        'uk' => self::QUOTE_STYLE_GUILLEMETS, // Ukrainian
        'da' => self::QUOTE_STYLE_GUILLEMETS, // Danish (also uses »…«)

        // Specific locale overrides (lowercase for normalization)
        'de_ch' => self::QUOTE_STYLE_GUILLEMETS, // Swiss German
        'pt_br' => self::QUOTE_STYLE_ENGLISH, // Brazilian Portuguese

        // =====================================================================
        // German style: „…" (low-high)
        // =====================================================================
        'de' => self::QUOTE_STYLE_GERMAN, // German
        'ka' => self::QUOTE_STYLE_GERMAN, // Georgian
        'cs' => self::QUOTE_STYLE_GERMAN, // Czech
        'et' => self::QUOTE_STYLE_GERMAN, // Estonian
        'is' => self::QUOTE_STYLE_GERMAN, // Icelandic
        'lt' => self::QUOTE_STYLE_GERMAN, // Lithuanian
        'mk' => self::QUOTE_STYLE_GERMAN, // Macedonian
        'ro' => self::QUOTE_STYLE_GERMAN, // Romanian
        'sk' => self::QUOTE_STYLE_GERMAN, // Slovak
        'sl' => self::QUOTE_STYLE_GERMAN, // Slovenian
        'pl' => self::QUOTE_STYLE_GERMAN, // Polish
        'hr' => self::QUOTE_STYLE_GERMAN, // Croatian
        'sr' => self::QUOTE_STYLE_GERMAN, // Serbian
        'bg' => self::QUOTE_STYLE_GERMAN, // Bulgarian
        'hu' => self::QUOTE_STYLE_GERMAN, // Hungarian

        // =====================================================================
        // English style: "…"
        // =====================================================================
        'en' => self::QUOTE_STYLE_ENGLISH,
        'af' => self::QUOTE_STYLE_ENGLISH, // Afrikaans
        'ar' => self::QUOTE_STYLE_ENGLISH, // Arabic
        'eo' => self::QUOTE_STYLE_ENGLISH, // Esperanto
        'id' => self::QUOTE_STYLE_ENGLISH, // Indonesian
        'ga' => self::QUOTE_STYLE_ENGLISH, // Irish
        'ko' => self::QUOTE_STYLE_ENGLISH, // Korean
        'br' => self::QUOTE_STYLE_ENGLISH, // Breton
        'th' => self::QUOTE_STYLE_ENGLISH, // Thai
        'tr' => self::QUOTE_STYLE_ENGLISH, // Turkish
        'vi' => self::QUOTE_STYLE_ENGLISH, // Vietnamese
        'nl' => self::QUOTE_STYLE_ENGLISH, // Dutch

        // =====================================================================
        // Finnish/Swedish style: "…" (same quote on both sides)
        // =====================================================================
        'fi' => self::QUOTE_STYLE_FINNISH, // Finnish
        'sv' => self::QUOTE_STYLE_FINNISH, // Swedish
        'bs' => self::QUOTE_STYLE_FINNISH, // Bosnian
    ];

    /**
     * Recommended fixer rules by locale.
     *
     * These are the default sets of fixers recommended for each locale.
     * You can customize this list when instantiating the Fixer class.
     */
    public const RECOMMENDED_RULES_BY_LOCALE = [
        // English
        'en_GB' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'SpaceBeforePunctuation', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],
        'en_US' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'SpaceBeforePunctuation', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],

        // French
        'fr_FR' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'SpaceBeforePunctuation', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],
        'fr_CA' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'SpaceBeforePunctuation', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],
        'fr_BE' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'SpaceBeforePunctuation', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],
        'fr_CH' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'SpaceBeforePunctuation', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],

        // German
        'de_DE' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'SpaceBeforePunctuation', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],
        'de_AT' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'SpaceBeforePunctuation', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],
        'de_CH' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'SpaceBeforePunctuation', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],

        // Other Western European
        'es_ES' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'SpaceBeforePunctuation', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],
        'it_IT' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'SpaceBeforePunctuation', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],
        'pt_PT' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'SpaceBeforePunctuation', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],
        'pt_BR' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'SpaceBeforePunctuation', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],
        'nl_NL' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'SpaceBeforePunctuation', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],
        'nl_BE' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'SpaceBeforePunctuation', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],
        'ca_ES' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'SpaceBeforePunctuation', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],

        // Nordic
        'sv_SE' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'SpaceBeforePunctuation', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],
        'da_DK' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'SpaceBeforePunctuation', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],
        'nb_NO' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'SpaceBeforePunctuation', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],
        'nn_NO' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'SpaceBeforePunctuation', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],
        'fi_FI' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'SpaceBeforePunctuation', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],

        // Central/Eastern European
        'pl_PL' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'SpaceBeforePunctuation', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],
        'cs_CZ' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'SpaceBeforePunctuation', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],
        'sk_SK' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'SpaceBeforePunctuation', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],
        'hu_HU' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'SpaceBeforePunctuation', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],
        'ro_RO' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'SpaceBeforePunctuation', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],

        // Slavic
        'ru_RU' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'SpaceBeforePunctuation', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],
        'uk_UA' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'SpaceBeforePunctuation', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],
        'be_BY' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'SpaceBeforePunctuation', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],
        'bg_BG' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'SpaceBeforePunctuation', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],
        'sr_RS' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'SpaceBeforePunctuation', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],
        'hr_HR' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'SpaceBeforePunctuation', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],

        // Other
        'el_GR' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'SpaceBeforePunctuation', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],
        'tr_TR' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'SpaceBeforePunctuation', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],
    ];

    /**
     * Get the spacing rule for a given locale.
     *
     * SPACING_RULE_FRENCH: Add non-breaking spaces before double punctuation (: ; ! ?)
     * SPACING_RULE_SWISS_GERMAN: French-style guillemets with thin spaces, no space before punctuation
     * SPACING_RULE_NONE: Remove any space before punctuation (default for most languages)
     */
    public static function getSpacingRule(string $locale): string
    {
        // Normalize locale (handle both fr_FR and fr-FR formats)
        $normalizedLocale = strtolower(str_replace('-', '_', $locale));

        // Check exact match first for locale exceptions
        return match ($normalizedLocale) {
            // Canadian French follows English conventions (no space)
            'fr_ca' => self::SPACING_RULE_NONE,
            // Swiss German uses French-style guillemets with thin spaces
            'de_ch' => self::SPACING_RULE_SWISS_GERMAN,
            // French locales use non-breaking spaces before double punctuation
            'fr', 'fr_fr', 'fr_be', 'fr_ch' => self::SPACING_RULE_FRENCH,
            // All other locales: check language fallback or default to none
            default => match (Fixer::getLanguageFromLocale($locale)) {
                'fr' => self::SPACING_RULE_FRENCH,
                default => self::SPACING_RULE_NONE,
            },
        };
    }

    /**
     * Get recommended rules for a given locale.
     *
     * @return array<string>|null Returns null if no specific rules are defined for this locale
     */
    public static function getRecommendedRules(string $locale): ?array
    {
        // Check exact match first (e.g., fr_CA)
        if (isset(self::RECOMMENDED_RULES_BY_LOCALE[$locale])) {
            return self::RECOMMENDED_RULES_BY_LOCALE[$locale];
        }

        // For locales not explicitly defined, return null
        // The caller can then decide to use a default or throw an exception
        return null;
    }

    /**
     * Get quotation style for a given locale.
     *
     * @return array{opening: string, openingSuffix: string, closing: string, closingPrefix: string}|null
     */
    public static function getQuotationStyle(string $locale): ?array
    {
        $style = self::getQuotationStyleType($locale);

        if (null === $style) {
            return null;
        }

        return match ($style) {
            self::QUOTE_STYLE_FRENCH => [
                'opening' => Fixer::LAQUO,
                'openingSuffix' => Fixer::NO_BREAK_SPACE,
                'closing' => Fixer::RAQUO,
                'closingPrefix' => Fixer::NO_BREAK_SPACE,
            ],
            self::QUOTE_STYLE_GUILLEMETS => [
                'opening' => Fixer::LAQUO,
                'openingSuffix' => '',
                'closing' => Fixer::RAQUO,
                'closingPrefix' => '',
            ],
            self::QUOTE_STYLE_GERMAN => [
                'opening' => Fixer::BDQUO,
                'openingSuffix' => '',
                'closing' => Fixer::LDQUO,
                'closingPrefix' => '',
            ],
            self::QUOTE_STYLE_ENGLISH => [
                'opening' => Fixer::LDQUO,
                'openingSuffix' => '',
                'closing' => Fixer::RDQUO,
                'closingPrefix' => '',
            ],
            self::QUOTE_STYLE_FINNISH => [
                'opening' => Fixer::RDQUO,
                'openingSuffix' => '',
                'closing' => Fixer::RDQUO,
                'closingPrefix' => '',
            ],
            default => null,
        };
    }

    /**
     * Get quotation style type for a given locale.
     */
    public static function getQuotationStyleType(string $locale): ?string
    {
        // Normalize locale (handle both fr_FR and fr-FR formats)
        $normalizedLocale = strtolower(str_replace('-', '_', $locale));

        // Check exact match first (e.g., pt_br, de_ch)
        if (isset(self::QUOTE_STYLES_BY_LOCALE[$normalizedLocale])) {
            return self::QUOTE_STYLES_BY_LOCALE[$normalizedLocale];
        }

        // Check language part (e.g., fr from fr_FR)
        $language = Fixer::getLanguageFromLocale($locale);
        if (isset(self::QUOTE_STYLES_BY_LOCALE[$language])) {
            return self::QUOTE_STYLES_BY_LOCALE[$language];
        }

        // No style defined for this locale
        return null;
    }
}

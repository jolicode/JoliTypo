<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo\Tests;

use JoliTypo\Fixer;
use JoliTypo\LocaleConfig;
use PHPUnit\Framework\TestCase;

class LocaleConfigTest extends TestCase
{
    // =========================================================================
    // Spacing Rules
    // =========================================================================

    public function testGetSpacingRuleFrench(): void
    {
        $this->assertSame(LocaleConfig::SPACING_RULE_FRENCH, LocaleConfig::getSpacingRule('fr'));
        $this->assertSame(LocaleConfig::SPACING_RULE_FRENCH, LocaleConfig::getSpacingRule('fr_FR'));
        $this->assertSame(LocaleConfig::SPACING_RULE_FRENCH, LocaleConfig::getSpacingRule('fr_BE'));
        $this->assertSame(LocaleConfig::SPACING_RULE_FRENCH, LocaleConfig::getSpacingRule('fr_CH'));
    }

    public function testGetSpacingRuleCanadianFrench(): void
    {
        // Canadian French uses no space before punctuation
        $this->assertSame(LocaleConfig::SPACING_RULE_NONE, LocaleConfig::getSpacingRule('fr_CA'));
    }

    public function testGetSpacingRuleSwissGerman(): void
    {
        $this->assertSame(LocaleConfig::SPACING_RULE_SWISS_GERMAN, LocaleConfig::getSpacingRule('de_CH'));
    }

    public function testGetSpacingRuleNone(): void
    {
        // Test various languages that should return SPACING_RULE_NONE
        $locales = ['en', 'en_GB', 'en_US', 'de', 'de_DE', 'es', 'it', 'pt', 'nl', 'pl', 'ru', 'cs'];

        foreach ($locales as $locale) {
            $this->assertSame(
                LocaleConfig::SPACING_RULE_NONE,
                LocaleConfig::getSpacingRule($locale),
                "Expected SPACING_RULE_NONE for locale: {$locale}"
            );
        }
    }

    public function testGetSpacingRuleUnknownLocaleFallsBackToNone(): void
    {
        $this->assertSame(LocaleConfig::SPACING_RULE_NONE, LocaleConfig::getSpacingRule('xx_XX'));
        $this->assertSame(LocaleConfig::SPACING_RULE_NONE, LocaleConfig::getSpacingRule('unknown'));
    }

    public function testGetSpacingRuleFallsBackToLanguage(): void
    {
        // fr_LU (Luxembourg French) is not explicitly defined, should fall back to 'fr'
        $this->assertSame(LocaleConfig::SPACING_RULE_FRENCH, LocaleConfig::getSpacingRule('fr_LU'));
    }

    // =========================================================================
    // Quotation Styles
    // =========================================================================

    public function testGetQuotationStyleFrench(): void
    {
        $style = LocaleConfig::getQuotationStyle('fr');

        $this->assertSame(Fixer::LAQUO, $style['opening']);
        $this->assertSame(Fixer::NO_BREAK_SPACE, $style['openingSuffix']);
        $this->assertSame(Fixer::RAQUO, $style['closing']);
        $this->assertSame(Fixer::NO_BREAK_SPACE, $style['closingPrefix']);
    }

    public function testGetQuotationStyleGuillemets(): void
    {
        $style = LocaleConfig::getQuotationStyle('ru');

        $this->assertSame(Fixer::LAQUO, $style['opening']);
        $this->assertSame('', $style['openingSuffix']);
        $this->assertSame(Fixer::RAQUO, $style['closing']);
        $this->assertSame('', $style['closingPrefix']);
    }

    public function testGetQuotationStyleGerman(): void
    {
        $style = LocaleConfig::getQuotationStyle('de');

        $this->assertSame(Fixer::BDQUO, $style['opening']);
        $this->assertSame('', $style['openingSuffix']);
        $this->assertSame(Fixer::LDQUO, $style['closing']);
        $this->assertSame('', $style['closingPrefix']);
    }

    public function testGetQuotationStyleEnglish(): void
    {
        $style = LocaleConfig::getQuotationStyle('en');

        $this->assertSame(Fixer::LDQUO, $style['opening']);
        $this->assertSame('', $style['openingSuffix']);
        $this->assertSame(Fixer::RDQUO, $style['closing']);
        $this->assertSame('', $style['closingPrefix']);
    }

    public function testGetQuotationStyleFinnish(): void
    {
        $style = LocaleConfig::getQuotationStyle('fi');

        // Finnish uses the same closing quote on both sides
        $this->assertSame(Fixer::RDQUO, $style['opening']);
        $this->assertSame('', $style['openingSuffix']);
        $this->assertSame(Fixer::RDQUO, $style['closing']);
        $this->assertSame('', $style['closingPrefix']);
    }

    public function testGetQuotationStyleUnknownReturnsNull(): void
    {
        $this->assertNull(LocaleConfig::getQuotationStyle('unknown'));
        $this->assertNull(LocaleConfig::getQuotationStyle('xx_XX'));
    }

    public function testGetQuotationStyleTypeReturnsCorrectTypes(): void
    {
        $this->assertSame(LocaleConfig::QUOTE_STYLE_FRENCH, LocaleConfig::getQuotationStyleType('fr'));
        $this->assertSame(LocaleConfig::QUOTE_STYLE_GUILLEMETS, LocaleConfig::getQuotationStyleType('ru'));
        $this->assertSame(LocaleConfig::QUOTE_STYLE_GERMAN, LocaleConfig::getQuotationStyleType('de'));
        $this->assertSame(LocaleConfig::QUOTE_STYLE_ENGLISH, LocaleConfig::getQuotationStyleType('en'));
        $this->assertSame(LocaleConfig::QUOTE_STYLE_FINNISH, LocaleConfig::getQuotationStyleType('fi'));
    }

    public function testGetQuotationStyleHandlesDashLocale(): void
    {
        // Should handle both fr_FR and fr-FR formats
        $style = LocaleConfig::getQuotationStyle('pt-BR');

        $this->assertSame(Fixer::LDQUO, $style['opening']);
        $this->assertSame(Fixer::RDQUO, $style['closing']);
    }

    // =========================================================================
    // Recommended Rules
    // =========================================================================

    public function testGetRecommendedRulesReturnsArrayForKnownLocale(): void
    {
        $rules = LocaleConfig::getRecommendedRules('en_GB');

        $this->assertIsArray($rules);
        $this->assertContains('Ellipsis', $rules);
        $this->assertContains('SmartQuotes', $rules);
        $this->assertContains('SpaceBeforePunctuation', $rules);
    }

    public function testGetRecommendedRulesReturnsNullForUnknownLocale(): void
    {
        $this->assertNull(LocaleConfig::getRecommendedRules('unknown'));
        $this->assertNull(LocaleConfig::getRecommendedRules('xx_XX'));
    }

    public function testRecommendedRulesIncludeSpaceBeforePunctuation(): void
    {
        // All recommended rules should include SpaceBeforePunctuation
        foreach (LocaleConfig::RECOMMENDED_RULES_BY_LOCALE as $locale => $rules) {
            $this->assertContains(
                'SpaceBeforePunctuation',
                $rules,
                "SpaceBeforePunctuation should be in recommended rules for {$locale}"
            );
        }
    }

    public function testRecommendedRulesStartWithUnicodeNormalization(): void
    {
        // Normalization must run before the other fixers, so that they see composed characters
        foreach (LocaleConfig::RECOMMENDED_RULES_BY_LOCALE as $locale => $rules) {
            $this->assertSame(
                'UnicodeNormalization',
                $rules[0],
                "UnicodeNormalization should be the first recommended rule for {$locale}"
            );
        }
    }

    // =========================================================================
    // Nested quotation styles
    // =========================================================================

    public function testGetNestedQuotationStyleTypeDerivedFromPrimaryStyle(): void
    {
        $this->assertSame(LocaleConfig::QUOTE_STYLE_SINGLE_ENGLISH, LocaleConfig::getNestedQuotationStyleType('en'));
        $this->assertSame(LocaleConfig::QUOTE_STYLE_SINGLE_ENGLISH, LocaleConfig::getNestedQuotationStyleType('en_US'));
        $this->assertSame(LocaleConfig::QUOTE_STYLE_SINGLE_ENGLISH, LocaleConfig::getNestedQuotationStyleType('nl'));
        $this->assertSame(LocaleConfig::QUOTE_STYLE_SINGLE_GERMAN, LocaleConfig::getNestedQuotationStyleType('de'));
        $this->assertSame(LocaleConfig::QUOTE_STYLE_SINGLE_GERMAN, LocaleConfig::getNestedQuotationStyleType('de_DE'));
        $this->assertSame(LocaleConfig::QUOTE_STYLE_SINGLE_GERMAN, LocaleConfig::getNestedQuotationStyleType('cs'));
        $this->assertSame(LocaleConfig::QUOTE_STYLE_SINGLE_GUILLEMETS, LocaleConfig::getNestedQuotationStyleType('de_CH'));
        $this->assertSame(LocaleConfig::QUOTE_STYLE_SINGLE_GUILLEMETS, LocaleConfig::getNestedQuotationStyleType('de-CH'));
        $this->assertSame(LocaleConfig::QUOTE_STYLE_SINGLE_FINNISH, LocaleConfig::getNestedQuotationStyleType('fi'));
        $this->assertSame(LocaleConfig::QUOTE_STYLE_SINGLE_FINNISH, LocaleConfig::getNestedQuotationStyleType('sv_SE'));
    }

    public function testGetNestedQuotationStyleTypeExceptions(): void
    {
        $this->assertSame(LocaleConfig::QUOTE_STYLE_ENGLISH, LocaleConfig::getNestedQuotationStyleType('fr'));
        $this->assertSame(LocaleConfig::QUOTE_STYLE_ENGLISH, LocaleConfig::getNestedQuotationStyleType('fr_FR'));
        $this->assertSame(LocaleConfig::QUOTE_STYLE_ENGLISH, LocaleConfig::getNestedQuotationStyleType('fr-CA'));
        $this->assertSame(LocaleConfig::QUOTE_STYLE_ENGLISH, LocaleConfig::getNestedQuotationStyleType('es'));
        $this->assertSame(LocaleConfig::QUOTE_STYLE_ENGLISH, LocaleConfig::getNestedQuotationStyleType('it_IT'));
        $this->assertSame(LocaleConfig::QUOTE_STYLE_ENGLISH, LocaleConfig::getNestedQuotationStyleType('pt_PT'));
        $this->assertSame(LocaleConfig::QUOTE_STYLE_SINGLE_ENGLISH, LocaleConfig::getNestedQuotationStyleType('pt_BR'));
        $this->assertSame(LocaleConfig::QUOTE_STYLE_GERMAN, LocaleConfig::getNestedQuotationStyleType('ru'));
        $this->assertSame(LocaleConfig::QUOTE_STYLE_GERMAN, LocaleConfig::getNestedQuotationStyleType('uk_UA'));
        $this->assertSame(LocaleConfig::QUOTE_STYLE_GUILLEMETS, LocaleConfig::getNestedQuotationStyleType('pl'));
        $this->assertSame(LocaleConfig::QUOTE_STYLE_GUILLEMETS, LocaleConfig::getNestedQuotationStyleType('ro_RO'));
    }

    public function testGetNestedQuotationStyleTypeUnknownLocale(): void
    {
        $this->assertNull(LocaleConfig::getNestedQuotationStyleType('xx_XX'));
        $this->assertNull(LocaleConfig::getNestedQuotationStyleType('unknown'));
    }

    public function testGetNestedQuotationStyle(): void
    {
        $this->assertSame(
            ['opening' => Fixer::LSQUO, 'openingSuffix' => '', 'closing' => Fixer::RSQUO, 'closingPrefix' => ''],
            LocaleConfig::getNestedQuotationStyle('en')
        );
        $this->assertSame(
            ['opening' => Fixer::SBQUO, 'openingSuffix' => '', 'closing' => Fixer::LSQUO, 'closingPrefix' => ''],
            LocaleConfig::getNestedQuotationStyle('de')
        );
        $this->assertSame(
            ['opening' => Fixer::LSAQUO, 'openingSuffix' => '', 'closing' => Fixer::RSAQUO, 'closingPrefix' => ''],
            LocaleConfig::getNestedQuotationStyle('de_CH')
        );
        $this->assertSame(
            ['opening' => Fixer::LDQUO, 'openingSuffix' => '', 'closing' => Fixer::RDQUO, 'closingPrefix' => ''],
            LocaleConfig::getNestedQuotationStyle('fr')
        );
        $this->assertSame(
            ['opening' => Fixer::BDQUO, 'openingSuffix' => '', 'closing' => Fixer::LDQUO, 'closingPrefix' => ''],
            LocaleConfig::getNestedQuotationStyle('ru')
        );
        $this->assertSame(
            ['opening' => Fixer::RSQUO, 'openingSuffix' => '', 'closing' => Fixer::RSQUO, 'closingPrefix' => ''],
            LocaleConfig::getNestedQuotationStyle('sv')
        );
        $this->assertNull(LocaleConfig::getNestedQuotationStyle('unknown'));
    }

    public function testRecommendedRulesPutSmartQuotesBeforeCurlyQuote(): void
    {
        // SmartQuotes converts the pairs of single quotes, CurlyQuote would turn their closing quote into an apostrophe
        foreach (LocaleConfig::RECOMMENDED_RULES_BY_LOCALE as $locale => $rules) {
            $smartQuotes = array_search('SmartQuotes', $rules, true);
            $curlyQuote = array_search('CurlyQuote', $rules, true);

            $this->assertNotFalse($smartQuotes, "SmartQuotes should be in recommended rules for {$locale}");
            $this->assertNotFalse($curlyQuote, "CurlyQuote should be in recommended rules for {$locale}");
            $this->assertLessThan($curlyQuote, $smartQuotes, "SmartQuotes must run before CurlyQuote for {$locale}");
        }
    }
}

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
}

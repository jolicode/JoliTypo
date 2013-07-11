<?php

namespace JoliTypo\Fixer;

use JoliTypo\Fixer;
use JoliTypo\FixerInterface;
use JoliTypo\LocaleAwareFixerInterface;
use JoliTypo\StateBag;
use Org\Heigl\Hyphenator\Hyphenator;

class Hyphen implements FixerInterface, LocaleAwareFixerInterface
{
    /**
     * @var Hyphenator
     */
    private $hyphenator;

    /**
     * @var array
     */
    private $supported_locales = array(
        "af_ZA", "ca", "da_DK", "de_AT", "de_CH", "de_DE", "en_GB", "en_UK", "et_EE", "fr", "hr_HR", "hu_HU", "it_IT", "lt_LT", "nb_NO", "nn_NO", "nl_NL", "pl_PL", "pt_BR", "ro_RO", "ru_RU", "sk_SK", "sl_SI", "sr", "zu_ZA"
    );

    public function __construct($locale)
    {
        $this->hyphenator = Hyphenator::factory(null, $this->fixLocale($locale));
        $this->setOptions();
    }

    /**
     * @param $locale
     */
    public function setLocale($locale)
    {
        $this->hyphenator = Hyphenator::factory(null, $this->fixLocale($locale));
        $this->setOptions();
    }

    protected function setOptions()
    {
        $this->hyphenator->getOptions()->setHyphen(Fixer::SHY);
        $this->hyphenator->getOptions()->setLeftMin(4);
        $this->hyphenator->getOptions()->setRightMin(3);
    }

    public function fix($content, StateBag $state_bag = null)
    {
        return $this->hyphenator->hyphenate($content);
    }

    /**
     * Transform fr_FR to fr to fit the list of supported locales
     *
     * @param $locale
     * @return mixed
     */
    protected function fixLocale($locale)
    {
        if (in_array($locale, $this->supported_locales)) {
            return $locale;
        }

        if (($short = Fixer::getLanguageFromLocale($locale)) !== $locale) {
            if (in_array($short, $this->supported_locales)) {
                return $short;
            }
        }

        // If no better locale found...
        return $locale;
    }
}

<?php

namespace JoliTypo\Fixer;

use JoliTypo\Fixer;
use JoliTypo\FixerInterface;
use JoliTypo\StateBag;
use Org\Heigl\Hyphenator\Hyphenator;

class Hyphen implements FixerInterface
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

    function __construct($locale)
    {
        // @todo Fix the locale with the supported ones from Org.
        $this->hyphenator = Hyphenator::factory(null, $this->fixLocale($locale));
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

        if (strpos($locale, '_')) {
            $parts = explode('_', $locale);
            if (in_array(strtolower($parts[0]), $this->supported_locales)) {
                return $parts[0];
            }
        }

        // If no better locale found...
        return $locale;
    }
}

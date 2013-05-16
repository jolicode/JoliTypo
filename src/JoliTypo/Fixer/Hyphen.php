<?php

namespace JoliTypo\Fixer;

use JoliTypo\Fixer;
use JoliTypo\FixerInterface;
use Org\Heigl\Hyphenator\Hyphenator;
use Org\Heigl\Hyphenator\Options;

class Hyphen implements FixerInterface
{
    /**
     * @var Hyphenator
     */
    private $hyphenator;

    function __construct($locale)
    {
        $this->hyphenator = Hyphenator::factory(null, $locale);
        $this->hyphenator->getOptions()->setHyphen(Fixer::SHY);
        $this->hyphenator->getOptions()->setLeftMin(4);
        $this->hyphenator->getOptions()->setRightMin(2);
    }

    public function fix($content)
    {
        return $this->hyphenator->hyphenate($content);
    }
}

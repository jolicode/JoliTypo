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
    }

    public function fix($content)
    {
        return $this->hyphenator->hyphenate($content);
    }
}

<?php

namespace JoliTypo\Fixer;

use JoliTypo\Fixer;
use JoliTypo\FixerInterface;
use JoliTypo\StateBag;

/**
 * Replace straight quotes by curly one's.
 *
 * @see http://practicaltypography.com/straight-and-curly-quotes.html
 */
class CurlyQuote implements FixerInterface
{
    public function fix($content, StateBag $state_bag = null)
    {
        return preg_replace('@([a-z])\'([a-z])@im', "$1".Fixer::RSQUO."$2", $content);
    }
}

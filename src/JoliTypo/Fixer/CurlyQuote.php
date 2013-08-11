<?php

namespace JoliTypo\Fixer;

use JoliTypo\Fixer;
use JoliTypo\FixerInterface;
use JoliTypo\StateBag;

class CurlyQuote implements FixerInterface
{
    public function fix($content, StateBag $state_bag = null)
    {
        return preg_replace('@([a-z])\'@im', "$1".Fixer::RSQUO, $content);
    }
}

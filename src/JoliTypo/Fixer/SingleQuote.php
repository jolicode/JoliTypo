<?php

namespace JoliTypo\Fixer;

use JoliTypo\Fixer;
use JoliTypo\FixerInterface;

class SingleQuote implements FixerInterface
{
    public function fix($content)
    {
        return preg_replace('@([a-z])\'([a-z])@im', "$1".Fixer::RSQUO."$2", $content);
    }
}

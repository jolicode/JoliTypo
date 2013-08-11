<?php

namespace JoliTypo\Fixer;

use JoliTypo\Fixer;
use JoliTypo\FixerInterface;
use JoliTypo\StateBag;

class Trademark implements FixerInterface
{
    public function fix($content, StateBag $state_bag = null)
    {
        $content = preg_replace('@\(tm\)@i', Fixer::TRADE, $content);
        $content = preg_replace('@\(c\) ([0-9]+)@i', Fixer::COPY.Fixer::NO_BREAK_SPACE.'$1', $content);
        $content = preg_replace('@\(c\)@i', Fixer::COPY, $content);
        $content = preg_replace('@\(r\)@i', Fixer::REG, $content);

        return $content;
    }
}

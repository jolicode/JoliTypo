<?php

namespace JoliTypo\Fixer;

use JoliTypo\Fixer;
use JoliTypo\FixerInterface;
use JoliTypo\StateBag;

class Dimension implements FixerInterface
{
    public function fix($content, StateBag $state_bag = null)
    {
        $content = preg_replace('@(\d++)( ?)x\\2(?=\d)@', '$1'.Fixer::TIMES, $content);

        return $content;
    }
}

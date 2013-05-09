<?php

namespace JoliTypo\Fixer;

use JoliTypo\Fixer;
use JoliTypo\FixerInterface;

class Dimension implements FixerInterface
{
    public function fix($content)
    {
        $content = preg_replace('@(\d++)( ?)x\\2(?=\d)@', '$1'.Fixer::TIMES, $content);

        return $content;
    }
}

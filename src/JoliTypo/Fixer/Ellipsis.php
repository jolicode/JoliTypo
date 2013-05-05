<?php

namespace JoliTypo\Fixer;

use JoliTypo\Fixer;
use JoliTypo\FixerInterface;

class Ellipsis implements FixerInterface
{
    public function fix($content)
    {
        $content = preg_replace('@\.{3,}@', Fixer::ELLIPSIS, $content);

        return $content;
    }
}

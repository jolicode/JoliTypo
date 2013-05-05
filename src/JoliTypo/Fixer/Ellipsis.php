<?php

namespace JoliTypo\Fixer;

use JoliTypo\Fixer;

class Ellipsis
{
    public function fix($content)
    {
        $content = preg_replace('@\.{3,}@', Fixer::ELLIPSIS, $content);

        return $content;
    }
}

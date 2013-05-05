<?php

namespace JoliTypo\Fixer;

use JoliTypo\Fixer;
use JoliTypo\FixerInterface;

class FrenchQuotes implements FixerInterface
{
    public function fix($content)
    {
        // @todo Not very clean but do the job
        $pattern = ($content{0} === '"') ? '@"([^"]+)"@im' : '@\s"([^"]+)"@im';
        $prefix  = ($content{0} === '"') ? '' : ' ';

        return preg_replace(
            $pattern,
            $prefix.Fixer::LAQUO.Fixer::NO_BREAK_THIN_SPACE."$1".Fixer::NO_BREAK_THIN_SPACE.Fixer::RAQUO,
            $content);
    }
}

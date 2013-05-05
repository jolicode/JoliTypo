<?php

namespace JoliTypo\Fixer;

use JoliTypo\Fixer;

class FrenchQuotes
{
    public function fix($content)
    {
        $replaceRules = array('@"([^"]+)"@im' => Fixer::LAQUO.Fixer::NO_BREAK_THIN_SPACE."$1".Fixer::NO_BREAK_THIN_SPACE.Fixer::RAQUO);

        return preg_replace(
            array_keys($replaceRules),
            array_values($replaceRules),
            $content);
    }
}

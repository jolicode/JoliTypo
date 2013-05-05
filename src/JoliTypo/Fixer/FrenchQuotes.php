<?php

namespace JoliTypo\Fixer;

use JoliTypo\Fixer;

class FrenchQuotes
{
    public function fix($content)
    {
        return preg_replace(
            '@ "([^"]+)"@im',
            ' '.Fixer::LAQUO.Fixer::NO_BREAK_THIN_SPACE."$1".Fixer::NO_BREAK_THIN_SPACE.Fixer::RAQUO,
            $content);
    }
}

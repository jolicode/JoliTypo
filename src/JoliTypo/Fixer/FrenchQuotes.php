<?php

namespace JoliTypo\Fixer;

use JoliTypo\Fixer;
use JoliTypo\FixerInterface;

class FrenchQuotes implements FixerInterface
{
    public function fix($content)
    {
        return preg_replace(
            '@\s"([^"]+)"@im',
            ' '.Fixer::LAQUO.Fixer::NO_BREAK_THIN_SPACE."$1".Fixer::NO_BREAK_THIN_SPACE.Fixer::RAQUO,
            $content);
    }
}

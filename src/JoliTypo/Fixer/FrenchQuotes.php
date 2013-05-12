<?php

namespace JoliTypo\Fixer;

use JoliTypo\Fixer;
use JoliTypo\FixerInterface;

/**
 * Use NO_BREAK_SPACE between the « » and the text as
 * recommended by "Abrégé du code typographique à l'usage de la presse", ISBN: 978-2351130667
 */
class FrenchQuotes implements FixerInterface
{
    public function fix($content)
    {
        return preg_replace(
            '@(^|\s)"([^"]+)"@im',
            "$1".Fixer::LAQUO.Fixer::NO_BREAK_SPACE."$2".Fixer::NO_BREAK_SPACE.Fixer::RAQUO,
            $content);
    }
}

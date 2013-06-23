<?php

namespace JoliTypo\Fixer;

use JoliTypo\Fixer;
use JoliTypo\FixerInterface;
use JoliTypo\StateBag;
use JoliTypo\StateNode;

/**
 * Use NO_BREAK_SPACE between the « » and the text as
 * recommended by "Abrégé du code typographique à l'usage de la presse", ISBN: 978-2351130667
 */
class FrenchQuotes extends BaseOpenClosePair implements FixerInterface
{
    public function fix($content, StateBag $state_bag = null)
    {
        // Fix simple cases
        $content = preg_replace('@(^|\s)"([^"]+)"@im',
            "$1".Fixer::LAQUO.Fixer::NO_BREAK_SPACE."$2".Fixer::NO_BREAK_SPACE.Fixer::RAQUO,
            $content);

        // Fix complex siblings cases
        if ($state_bag) {
            $content = $this->fixViaState($content, $state_bag, 'FrenchQuotesOpenSolo',
                '@(^|\s)"([^"]*)$@', '@(^|[^"]+)"(.+)@im', Fixer::LAQUO.Fixer::NO_BREAK_SPACE,
                    Fixer::NO_BREAK_SPACE.Fixer::RAQUO);
        }

        return $content;
    }
}

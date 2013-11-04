<?php

namespace JoliTypo\Fixer;

use JoliTypo\Fixer;
use JoliTypo\FixerInterface;
use JoliTypo\StateBag;

/**
 * Convert dumb quotes (" ") to smart quotes (“ ”).
 */
class EnglishQuotes extends BaseOpenClosePair implements FixerInterface
{
    public function fix($content, StateBag $state_bag = null)
    {
        // Fix complex siblings cases
        if ($state_bag) {
            $content = $this->fixViaState($content, $state_bag, 'EnglishQuotesOpenSolo',
                '@(^|\s|\()"([^"]*)$@', '@(^|[^"]+)"@im', Fixer::LDQUO, Fixer::RDQUO);
        }

        $content = preg_replace(
                    '@(^|\s|\()"([^"]+)"@im',
                    "$1".Fixer::LDQUO."$2".Fixer::RDQUO,
                    $content);

        return $content;
    }
}

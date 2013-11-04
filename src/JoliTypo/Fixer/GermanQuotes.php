<?php

namespace JoliTypo\Fixer;

use JoliTypo\Fixer;
use JoliTypo\FixerInterface;
use JoliTypo\StateBag;

/**
 * Convert dumb quotes (" ") to smart quotes („ “).
 */
class GermanQuotes extends BaseOpenClosePair implements FixerInterface
{
    public function fix($content, StateBag $state_bag = null)
    {
        // Fix complex siblings cases
        if ($state_bag) {
            $content = $this->fixViaState($content, $state_bag, 'GermanQuotesOpenSolo',
                '@(^|\s|\()"([^"]*)$@', '@(^|[^"]+)"@im', Fixer::BDQUO, Fixer::LDQUO);
        }

        $content = preg_replace(
                    '@(^|\s|\()"([^"]+)"@im',
                    "$1".Fixer::BDQUO."$2".Fixer::LDQUO,
                    $content);

        return $content;
    }
}

<?php

namespace JoliTypo\Fixer;

use JoliTypo\Fixer;
use JoliTypo\FixerInterface;
use JoliTypo\StateBag;

/**
 * Convert dumb quotes (" ") to smart quotes (“ ”).
 */
class EnglishQuotes implements FixerInterface
{
    public function fix($content, StateBag $state_bag = null)
    {
        return preg_replace(
            '@(^|\s)"([^"]+)"@im',
            "$1".Fixer::LDQUO."$2".Fixer::RDQUO,
            $content);
    }
}

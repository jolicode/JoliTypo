<?php

namespace JoliTypo\Fixer;

use JoliTypo\Fixer;
use JoliTypo\FixerInterface;

/**
 * Convert dumb quotes (" ") to smart quotes (“ ”).
 */
class EnglishQuotes implements FixerInterface
{
    public function fix($content)
    {
        return preg_replace(
            '@(^|\s)"([^"]+)"@im',
            "$1".Fixer::LDQUO."$2".Fixer::RDQUO,
            $content);
    }
}

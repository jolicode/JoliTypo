<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo\Fixer;

use JoliTypo\Fixer;
use JoliTypo\FixerInterface;
use JoliTypo\StateBag;

/**
 * Convert dumb quotes (" ") to smart quotes (“ ”).
 */
class EnglishQuotes extends BaseOpenClosePair implements FixerInterface
{
    public function fix($content, StateBag $stateBag = null)
    {
        // Fix complex siblings cases
        if ($stateBag) {
            $content = $this->fixViaState($content, $stateBag, 'EnglishQuotesOpenSolo',
                '@(^|\s|\()"([^"]*)$@', '@(^|[^"]+)"@im', Fixer::LDQUO, Fixer::RDQUO);
        }

        $content = preg_replace(
                    '@(^|\s|\()"([^"]+)"@im',
                    '$1'.Fixer::LDQUO.'$2'.Fixer::RDQUO,
                    $content);

        return $content;
    }
}

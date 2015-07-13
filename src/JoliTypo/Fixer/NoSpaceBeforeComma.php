<?php

namespace JoliTypo\Fixer;

use JoliTypo\Fixer;
use JoliTypo\FixerInterface;
use JoliTypo\StateBag;

/**
 * No space before comma (,)
 *
 * @package JoliTypo\Fixer
 */
class NoSpaceBeforeComma implements FixerInterface
{
    public function fix($content, StateBag $state_bag = null)
    {
        $content = preg_replace('@(\w+) *(,) *@mu', '$1$2 ', $content);

        return $content;
    }
}

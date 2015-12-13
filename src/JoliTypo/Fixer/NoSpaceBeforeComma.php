<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo\Fixer;

use JoliTypo\FixerInterface;
use JoliTypo\StateBag;

/**
 * No space before comma (,).
 */
class NoSpaceBeforeComma implements FixerInterface
{
    public function fix($content, StateBag $state_bag = null)
    {
        $content = preg_replace('@(\w+) *(,) *@mu', '$1$2 ', $content);

        return $content;
    }
}

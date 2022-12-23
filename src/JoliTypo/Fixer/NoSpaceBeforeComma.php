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
 * No space before comma (,).
 */
class NoSpaceBeforeComma implements FixerInterface
{
    public function fix(string $content, ?StateBag $stateBag = null)
    {
        return preg_replace('@([^\d\s]+)[' . Fixer::ALL_SPACES . ']*(,)[' . Fixer::ALL_SPACES . ']*@mu', '$1$2 ', $content);
    }
}

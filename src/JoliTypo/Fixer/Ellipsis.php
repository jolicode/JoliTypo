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

class Ellipsis implements FixerInterface
{
    public function fix(string $content, ?StateBag $stateBag = null)
    {
        return preg_replace('@\.{3,}@', Fixer::ELLIPSIS, $content);
    }
}

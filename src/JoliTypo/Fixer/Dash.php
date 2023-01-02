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

class Dash implements FixerInterface
{
    public function fix(string $content, ?StateBag $stateBag = null)
    {
        $content = preg_replace('@(?<=[0-9 ]|^)-(?=[0-9 ]|$)@', Fixer::NDASH, $content);

        return preg_replace('@ ?-- ?([^-]|$)@s', Fixer::MDASH . '$1', $content);
    }
}

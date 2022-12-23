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

class Trademark implements FixerInterface
{
    public function fix(string $content, ?StateBag $stateBag = null)
    {
        $content = preg_replace('@\(tm\)@i', Fixer::TRADE, $content);
        $content = preg_replace('@\(c\)[' . Fixer::ALL_SPACES . ']([0-9]+)@i', Fixer::COPY . Fixer::NO_BREAK_SPACE . '$1', $content);
        $content = preg_replace('@\(c\)@i', Fixer::COPY, $content);

        return preg_replace('@\(r\)@i', Fixer::REG, $content);
    }
}

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

class Dimension implements FixerInterface
{
    public function fix(string $content, ?StateBag $stateBag): string
    {
        return preg_replace('@(\d+["\']?)(' . Fixer::ALL_SPACES . ')?x(' . Fixer::ALL_SPACES . ')?(?=\d)@', '$1$2' . Fixer::TIMES . '$2', $content);
    }
}

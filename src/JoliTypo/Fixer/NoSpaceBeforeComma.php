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
    public function fix(string $content, ?StateBag $stateBag = null): string
    {
        return preg_replace(
            [
                // Remove spaces before the comma
                '@([^\d\s]+)[' . Fixer::ALL_SPACES . ']+(,)@mu',
                // Ensure exactly one space after the comma, unless a line break follows it
                '@([^\d\s])(,)[' . Fixer::ALL_SPACES . ']*+(?!\v)@mu',
            ],
            ['$1$2', '$1$2 '],
            $content
        );
    }
}

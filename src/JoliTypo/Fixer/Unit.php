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
 * Add nbsp between numeric and units.
 */
class Unit implements FixerInterface
{
    public function fix(string $content, ?StateBag $stateBag = null)
    {
        // Support a wide range of currencies
        return preg_replace('@([\dº])(' . Fixer::ALL_SPACES . ')+([º°%Ω฿₵¢₡$₫֏€ƒ₲₴₭£₤₺₦₨₱៛₹$₪৳₸₮₩¥\w]{1})@', '$1' . Fixer::NO_BREAK_SPACE . '$3', $content);
    }
}

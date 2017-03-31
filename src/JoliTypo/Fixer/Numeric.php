<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo\Fixer;

use JoliTypo\Fixer;

/**
 * {@inheritdoc}
 *
 * @deprecated Numeric should not be used (reserved keyword in PHP7)
 */
class Numeric extends Unit
{
    public function __construct()
    {
        trigger_error('Numeric fixer is deprecated, use Unit instead.', E_USER_NOTICE);
    }
}

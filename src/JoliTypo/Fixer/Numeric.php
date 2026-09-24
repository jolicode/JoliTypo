<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo\Fixer;

/**
 * @deprecated Numeric should not be used (reserved keyword in PHP7)
 */
class Numeric extends Unit
{
    #[\Deprecated(message: 'use Unit instead, it will be removed in 2.0', since: '1.0.2')]
    public function __construct()
    {
    }
}

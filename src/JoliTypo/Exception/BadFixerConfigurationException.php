<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo\Exception;

class BadFixerConfigurationException extends \InvalidArgumentException
{
    protected $message = 'Fixer needs configuration.';
}

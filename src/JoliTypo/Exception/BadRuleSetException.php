<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo\Exception;

class BadRuleSetException extends \InvalidArgumentException
{
    protected $message = 'RuleSet must be an array of Fixer names or instances.';
}

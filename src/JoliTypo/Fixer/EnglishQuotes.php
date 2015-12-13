<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo\Fixer;

/**
 * @deprecated Use SmartQuotes, to be removed in 2.0
 */
class EnglishQuotes extends SmartQuotes
{
    public function __construct($locale = null)
    {
        parent::__construct('en');
    }
}

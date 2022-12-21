<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo\Exception;

class InvalidMarkupException extends \RuntimeException
{
    protected $message = 'An error happened when trying to read your HTML with \\DOMDocument.';
}

<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

require __DIR__ . '/../vendor/autoload.php';

use JoliTypo\Fixer;

$fixer = new Fixer(['Ellipsis', 'Dash', 'SmartQuotes', 'CurlyQuote', 'Hyphen']);

echo $fixer->fix('<p>"Tell me Mr. Anderson... what good is a phone call... if you\'re unable to speak?" -- Agent Smith, <em>Matrix</em>.</p>');

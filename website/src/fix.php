<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

// Run in the browser by public/js/php.js, as the php command would:
//
//     php fix.php <locale> <fixer>... < content.html

require __DIR__ . '/../vendor/autoload.php';

use JoliTypo\Fixer;

[, $locale] = $argv;

$fixer = new Fixer(array_slice($argv, 2));
$fixer->setLocale($locale);

echo $fixer->fix(file_get_contents('php://stdin'));

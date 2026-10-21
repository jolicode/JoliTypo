<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

use Castor\Attribute\AsTask;

use function Castor\run;

defined('CASTOR_USE_CHDIR') || define('CASTOR_USE_CHDIR', true);

Castor\import(__DIR__ . '/tools/php-cs-fixer/');
Castor\import(__DIR__ . '/tools/phpstan/');
Castor\mount(__DIR__ . '/tools/cli/');
Castor\mount(__DIR__ . '/website/');
Castor\mount(__DIR__ . '/tools/phar/');

#[AsTask(description: 'Install dependencies')]
function install(): void
{
    run(['composer', 'install']);
}

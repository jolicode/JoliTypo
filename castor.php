<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

use Castor\Attribute\AsTask;

use function Castor\run;

Castor\import(__DIR__ . '/tools/php-cs-fixer/');
Castor\import(__DIR__ . '/tools/phpstan/');
Castor\import(__DIR__ . '/website/castor.php');
Castor\mount(__DIR__ . '/tools/cli/');
Castor\mount(__DIR__ . '/tools/phar/');

#[AsTask(description: 'Install dependencies')]
function install(): void
{
    run(['composer', 'install']);
}

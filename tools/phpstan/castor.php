<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace qa\phpstan;

use Castor\Attribute\AsTask;

use function Castor\context;
use function Castor\exit_code;
use function Castor\run;

#[AsTask(description: 'Run PHPStan', aliases: ['phpstan'])]
function phpstan(bool $generateBaseline = false): int
{
    if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
        install();
    }

    $command = [
        __DIR__ . '/vendor/bin/phpstan',
        '-v',
    ];

    if ($generateBaseline) {
        $command[] = '-b';
    }

    return exit_code($command);
}

#[AsTask(description: 'install dependencies')]
function install(): void
{
    run(['composer', 'install'], context()->withWorkingDirectory(__DIR__));
}

#[AsTask(description: 'update dependencies')]
function update(): void
{
    run(['composer', 'update'], context()->withWorkingDirectory(__DIR__));
}

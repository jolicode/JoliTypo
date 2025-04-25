<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace cli;

use Castor\Attribute\AsTask;

use function Castor\io;
use function Castor\run;

#[AsTask(description: 'install dependencies')]
function install(): void
{
    io()->title('Install phar dependencies');

    run(['composer', 'install']);
}

#[AsTask(description: 'update dependencies')]
function update(): void
{
    io()->title('Update phar dependencies');
    run(['composer', 'update']);
}

<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace phar;

use Castor\Attribute\AsTask;

use function Castor\context;
use function Castor\io;
use function Castor\run;
use function Castor\with;

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

#[AsTask(description: 'Compile phar')]
function compile()
{
    with(
        function () {
            if (!is_dir(__DIR__ . '/../cli/vendor')) {
                \cli\install();
            }
        },
        context: context()->withWorkingDirectory(__DIR__ . '/../cli')
    );

    if (!is_dir(__DIR__ . '/vendor')) {
        install();
    }

    io()->title('Compile phar');
    run(['vendor/bin/box', 'compile']);
}

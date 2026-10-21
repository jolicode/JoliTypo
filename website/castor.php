<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace website;

use Castor\Attribute\AsOption;
use Castor\Attribute\AsTask;
use PhpWasm\Stack;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;

use function Castor\context;
use function Castor\finder;
use function Castor\fs;
use function Castor\http_request;
use function Castor\io;
use function Castor\run as do_run;
use function PhpWasm\build;
use function PhpWasm\check_stack;
use function PhpWasm\test_page;

#[AsTask(description: 'Install dependencies')]
function install(): void
{
    io()->title('Installing dependencies');

    run(['composer', 'install', '--no-dev', '--optimize-autoloader']);
}

#[AsTask(description: 'Update dependencies')]
function update(): void
{
    io()->title('Installing dependencies');

    run(['composer', 'update', '--no-dev', '--optimize-autoloader']);
}

#[AsTask('wasm:build', description: 'Build PHP as WebAssembly with the demo code, and export it to the public folder with the footer')]
function wasm_build(
    #[AsOption(description: 'An override given to "docker buildx bake --set", a cache for example', mode: InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY)]
    array $set = [],
): void {
    io()->title('Building PHP as WebAssembly');

    wasm_app();
    build([wasm_stack()], $set);

    footer();
}

#[AsTask('wasm:check', description: 'Check from node, without a browser, that the wasm build boots and that the page fixes a text')]
function wasm_check(): void
{
    io()->title('Checking the wasm build');

    check_stack(wasm_stack());
    test_page(__DIR__ . '/tests/page.test.mjs');
}

/**
 * The PHP that runs the demo in the browser, see https://github.com/lyrixx/php-wasm-builder.
 */
function wasm_stack(): Stack
{
    return Stack::named('jolitypo-demo')
        ->php('8.5.12')
        // What JoliTypo requires
        ->extensions('dom', 'mbstring')
        // Filled by wasm_app(), js/wasm.js runs the PHP files of /app
        ->embed(__DIR__ . '/build/app', 'app')
        ->targets('web')
        ->outputDir(__DIR__ . '/public/build')
    ;
}

/**
 * Copies the code PHP runs in the browser to the directory the stack embeds.
 */
function wasm_app(): void
{
    $app = wasm_stack()->embed;

    // Embedded files take some of the memory of the WASM module: leave out what PHP never reads
    fs()->remove($app);
    fs()->mirror(__DIR__, $app, finder()
        ->in([__DIR__ . '/src', __DIR__ . '/vendor'])
        ->exclude(['jolicode/jolitypo/src/JoliTypo/Bridge', 'org_heigl/hyphenator/src/tools'])
        // The hyphenator reads the .ini dictionaries, built from the .dic ones
        ->notName(['*.dic', '*.md', '*.txt', 'CHANGELOG', 'composer.*', 'LICENSE', 'phpstan.neon'])
    );
}

const JOLI_FOOTER_URL = 'https://raw.githubusercontent.com/jolicode/oss-theme/refs/heads/main/snippet-joli-footer.html';

#[AsTask(description: 'Download the JoliCode open-source footer (jolicode/oss-theme) to the public folder')]
function footer(): void
{
    io()->title('Downloading the JoliCode footer');

    $footer = http_request('GET', JOLI_FOOTER_URL)->getContent();
    $footer = strtr($footer, [
        '#GITHUB_REPO' => 'jolicode/JoliTypo',
        '<!-- #SUBTITLE -->' => 'JoliTypo is licensed under <a href="https://github.com/jolicode/JoliTypo/blob/main/LICENSE" target="_blank" rel="noreferrer noopener" class="jf-link">MIT license</a>',
    ]);

    fs()->dumpFile(__DIR__ . '/public/joli-footer.html', $footer);
}

#[AsTask(description: 'Run the server')]
function serve(string $address = 'localhost:9999'): void
{
    io()->title("Serving on http://{$address}");

    run(['php', '-S', $address, '-t', 'public']);
}

#[AsTask(description: 'Run the headless browser smoke test of the demo (needs Node 22+, Chrome and the wasm build)')]
function smoke(): void
{
    io()->title('Running the demo smoke test');

    run(['node', 'tests/smoke.mjs']);
}

function run(array $command): Process
{
    return do_run($command, context: context()->withWorkingDirectory(__DIR__));
}

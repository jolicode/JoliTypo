<?php

$header = <<<EOF
This file is part of JoliTypo - a project by JoliCode.

This software consists of voluntary contributions made by many individuals
and is licensed under the MIT license.
EOF;

Symfony\CS\Fixer\Contrib\HeaderCommentFixer::setHeader($header);

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(array(__DIR__.'/src', __DIR__.'/tests'))
;

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->fixers(array(
        'header_comment',
        '-unalign_double_arrow',
        '-unalign_equals',
        'align_double_arrow',
        'newline_after_open_tag',
        'ordered_use',
    ))
    ->setUsingCache(true)
    ->finder($finder)
;

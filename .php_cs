<?php

$header = <<<EOF
This file is part of JoliTypo - a project by JoliCode.

This software consists of voluntary contributions made by many individuals
and is licensed under the MIT license.
EOF;

$finder = PhpCsFixer\Finder::create()
    ->in(array(__DIR__.'/src', __DIR__.'/tests'))
;

return PhpCsFixer\Config::create()
    ->setRules(array(
        '@Symfony' => true,
        'header_comment' => array('header' => $header),
        'binary_operator_spaces' => array(
            'align_double_arrow' => true,
            'align_equals' => true,
        ),
        'array_syntax' => array('syntax' => 'long'),
        'ordered_imports' => true
    ))
    ->setFinder($finder)
;

<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo;

interface FixerInterface
{
    /**
     * @param string        $content  A string to fix
     * @param StateBag|null $stateBag A bag of useful information
     */
    public function fix(string $content, ?StateBag $stateBag): string;
}

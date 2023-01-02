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
     * @return string
     */
    public function fix(string $content, ?StateBag $stateBag = null);
}

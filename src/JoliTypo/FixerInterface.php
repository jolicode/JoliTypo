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
     * @param string   $content   A string to fix
     * @param StateBag $state_bag A bag of useful informations
     *
     * @return string
     */
    public function fix($content, StateBag $state_bag = null);
}

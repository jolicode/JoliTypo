<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo\Fixer;

use JoliTypo\FixerInterface;
use JoliTypo\StateBag;

/**
 * NO_BREAK_SPACE before :
 * NO_BREAK_THIN_SPACE before ; : ! ?
 * NO_BREAK_SPACE inside « ».
 *
 * As recommended by "Abrégé du code typographique à l'usage de la presse", ISBN: 978-2351130667
 *
 * @deprecated since 1.7.0, use SpaceBeforePunctuation instead
 */
class FrenchNoBreakSpace implements FixerInterface
{
    private SpaceBeforePunctuation $delegate;

    public function __construct()
    {
        $this->delegate = new SpaceBeforePunctuation('fr_FR');
    }

    public function fix(string $content, ?StateBag $stateBag = null): string
    {
        return $this->delegate->fix($content, $stateBag);
    }
}

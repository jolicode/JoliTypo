<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo\Fixer;

use JoliTypo\Fixer;
use JoliTypo\FixerInterface;
use JoliTypo\StateBag;

/**
 * NO_BREAK_SPACE before :
 * NO_BREAK_THIN_SPACE before ; : ! ?
 * NO_BREAK_SPACE inside « ».
 *
 * As recommended by "Abrégé du code typographique à l'usage de la presse", ISBN: 978-2351130667
 */
class FrenchNoBreakSpace implements FixerInterface
{
    public function fix(string $content, ?StateBag $stateBag = null)
    {
        $content = preg_replace('@[' . Fixer::ALL_SPACES . ']+(:)@mu', Fixer::NO_BREAK_SPACE . '$1', $content);
        $content = preg_replace('@[' . Fixer::ALL_SPACES . ']+([;!\?])@mu', Fixer::NO_BREAK_THIN_SPACE . '$1', $content);

        $content = preg_replace('@' . Fixer::LAQUO . '[' . Fixer::ALL_SPACES . ']?@mu', Fixer::LAQUO . Fixer::NO_BREAK_SPACE, $content);

        return preg_replace('@[' . Fixer::ALL_SPACES . ']?' . Fixer::RAQUO . '@mu', Fixer::NO_BREAK_SPACE . Fixer::RAQUO, $content);
    }
}

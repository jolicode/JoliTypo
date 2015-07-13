<?php

namespace JoliTypo\Fixer;

use JoliTypo\Fixer;
use JoliTypo\FixerInterface;
use JoliTypo\StateBag;

/**
 * NO_SPACE before ,
 * As recommended by "Abrégé du code typographique à l'usage de la presse", ISBN: 978-2351130667
 *
 * @package JoliTypo\Fixer
 */
class NoSpaceBeforeComma implements FixerInterface
{
    public function fix($content, StateBag $state_bag = null)
    {
        $content = preg_replace('@(\w+) *(,) *'.Fixer::NO_BREAK_SPACE.'*@mu', '$1$2 ', $content);

        return $content;
    }
}

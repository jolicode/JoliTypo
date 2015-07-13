<?php

namespace JoliTypo\Fixer;

use JoliTypo\Fixer;
use JoliTypo\FixerInterface;
use JoliTypo\StateBag;

/**
 * NO_BREAK_SPACE before :
 * NO_BREAK_THIN_SPACE before ; : ! ?
 * NO_BREAK_SPACE inside « »
 *
 * As recommended by "Abrégé du code typographique à l'usage de la presse", ISBN: 978-2351130667
 *
 * @package JoliTypo\Fixer
 */
class FrenchNoBreakSpace implements FixerInterface
{
    public function fix($content, StateBag $state_bag = null)
    {
        $spaces = '\s'.Fixer::NO_BREAK_SPACE.Fixer::NO_BREAK_THIN_SPACE;

        $content = preg_replace('@['.$spaces.']+(:)@mu', Fixer::NO_BREAK_SPACE.'$1', $content);
        $content = preg_replace('@['.$spaces.']+([;!\?])@mu', Fixer::NO_BREAK_THIN_SPACE.'$1', $content);

        $content = preg_replace('@'.Fixer::LAQUO.'['.$spaces.']?@mu', Fixer::LAQUO.Fixer::NO_BREAK_SPACE, $content);
        $content = preg_replace('@['.$spaces.']?'.Fixer::RAQUO.'@mu', Fixer::NO_BREAK_SPACE.Fixer::RAQUO, $content);

        return $content;
    }
}

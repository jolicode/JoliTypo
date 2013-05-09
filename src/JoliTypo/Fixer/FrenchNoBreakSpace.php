<?php

namespace JoliTypo\Fixer;

use JoliTypo\Fixer;
use JoliTypo\FixerInterface;

/**
 * We use NO_BREAK_SPACE, but some recommends NO_BREAK_THIN_SPACE. Actually, both are correct.
 *
 * @package JoliTypo\Fixer
 */
class FrenchNoBreakSpace implements FixerInterface
{
    public function fix($content)
    {
        return preg_replace('@[\s'.Fixer::NO_BREAK_SPACE.']+([!\?:;‽])@im', Fixer::NO_BREAK_SPACE.'$1', $content);
    }
}

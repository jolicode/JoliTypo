<?php

namespace JoliTypo\Fixer;

use JoliTypo\Fixer;
use JoliTypo\FixerInterface;

class FrenchNoBreakSpace implements FixerInterface
{
    public function fix($content)
    {
        return preg_replace('@[\s'.Fixer::NO_BREAK_SPACE.']+([\!\?\:\;])@im', Fixer::NO_BREAK_SPACE.'$1', $content);
    }
}

<?php

namespace JoliTypo\Fixer;

use JoliTypo\Fixer;
use JoliTypo\FixerInterface;

class Dash implements FixerInterface
{
    public function fix($content)
    {
        $content = preg_replace('@(?<=[\d ]|^)-(?=[\d ]|$)@', Fixer::NDASH, $content);
        //$content = preg_replace('#(?<=[^!*+,/:;<=>@\\\\_|-])--(?=[^!*+,/:;<=>@\\\\_|-])#', Fixer::NDASH, $content);


        $content = preg_replace("@\\s?--\\s?([^-]|$)@s", Fixer::MDASH."$1", $content);

        return $content;
    }
}

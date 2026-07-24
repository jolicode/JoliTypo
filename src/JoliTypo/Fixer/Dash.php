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

class Dash implements FixerInterface
{
    public function fix(string $content, ?StateBag $stateBag = null)
    {
        // Convert hyphen between numbers/spaces to en dash (date/number ranges)
        $content = preg_replace('@(?<=[0-9 ]|^)-(?=[0-9 ]|$)@', Fixer::NDASH, $content);

        // Convert double hyphens to em dash
        $content = preg_replace('@ ?-- ?([^-]|$)@s', Fixer::MDASH . '$1', $content);

        // Replace any space before an en or em dash with a narrow no-break space.
        // This prevents line breaks before dashes used as text separators (incises),
        // while still allowing a break after the dash.
        $content = preg_replace(
            '@[ ' . Fixer::NO_BREAK_SPACE . Fixer::NO_BREAK_THIN_SPACE . '](' . Fixer::NDASH . '|' . Fixer::MDASH . ')@u',
            Fixer::NO_BREAK_THIN_SPACE . '$1',
            $content
        );

        return $content;
    }
}

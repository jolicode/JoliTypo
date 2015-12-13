<?php

namespace JoliTypo\Fixer;

use JoliTypo\Fixer;
use JoliTypo\FixerInterface;
use JoliTypo\StateBag;

/**
 * NO_BREAK_SPACE before :
 * NO_BREAK_THIN_SPACE before ; : ! ?
 * NO_BREAK_SPACE inside « »
 * NO_BREAK_SPACE before common units (from judbd/php-typography)
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
        $units = '

            ### Temporal units
            (?:ms|s|secs?|mins?|hr?s?)\.?|
            milliseconde?s?|seconde?s?|minutes?|hours?|days?|years?|decades?|century|centuries|millennium|millennia|
            heures?|jours?|années?|décennie?|siècles?|centenaires?|millénaires?|

            ### Imperial units
            (?:in|ft|yd|mi)\.?|
            (?:ac|ha|oz|pt|qt|gal|lb|st)\.?
            s\.f\.|sf|s\.i\.|si|square[ ]feet|square[ ]foot|
            inch|inches|foot|feet|yards?|miles?|acres?|hectares?|ounces?|pints?|quarts?|gallons?|pounds?|stones?|

            ### Metric units (with prefixes)
            (?:p|µ|[mcdhkMGT])?
            (?:[mgstAKNJWCVFSTHBL]|mol|cd|rad|Hz|Pa|Wb|lm|lx|Bq|Gy|Sv|kat|Ω|Ohm|&Omega;|&\#0*937;|&\#[xX]0*3[Aa]9;)|
            (?:nano|micro|milli|centi|deci|deka|hecto|kilo|mega|giga|tera)?
            (?:liters?|meters?|grams?|newtons?|pascals?|watts?|joules?|amperes?)|
            (?:litres?|mètres?|grammes?|ampères?)|

            ### Computers units (KB, Kb, TB, Kbps)
            [kKMGT]?(?:[oBb]|[oBb]ps|flops)|

            ### Money
            ¢|M?(?:£|¥|€|\$)|
            livres?|yens?|euros?|dollars?|

            ### Other units
            °[CF]? |
            %|pi|M?px|em|en|[NSEOW]|[NS][EOW]|mbar

        '; // required modifiers: x (multiline pattern)

        $content = preg_replace('@['.$spaces.']+(:)@mu', Fixer::NO_BREAK_SPACE.'$1', $content);
        $content = preg_replace('@['.$spaces.']+([;!\?])@mu', Fixer::NO_BREAK_THIN_SPACE.'$1', $content);

        $content = preg_replace('@('.$units.')([\d])@mux', '$1 $2', $content);
        $content = preg_replace('@([\d])['.$spaces.']?('.$units.')([^\w]|$)@mux', '$1'.Fixer::NO_BREAK_SPACE.'$2$3', $content);
        $content = preg_replace('@('.Fixer::NO_BREAK_SPACE.'(?:'.$units.'))['.$spaces.']([\d])@mux', '$1'.Fixer::NO_BREAK_SPACE.'$2', $content);

        $content = preg_replace('@'.Fixer::LAQUO.'['.$spaces.']?@mu', Fixer::LAQUO.Fixer::NO_BREAK_SPACE, $content);
        $content = preg_replace('@['.$spaces.']?'.Fixer::RAQUO.'@mu', Fixer::NO_BREAK_SPACE.Fixer::RAQUO, $content);

        return $content;
    }
}

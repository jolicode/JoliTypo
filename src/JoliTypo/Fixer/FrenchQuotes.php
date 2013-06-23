<?php

namespace JoliTypo\Fixer;

use JoliTypo\Fixer;
use JoliTypo\FixerInterface;
use JoliTypo\StateBag;
use JoliTypo\StateNode;

/**
 * Use NO_BREAK_SPACE between the « » and the text as
 * recommended by "Abrégé du code typographique à l'usage de la presse", ISBN: 978-2351130667
 */
class FrenchQuotes implements FixerInterface
{
    public function fix($content, StateBag $state_bag = null)
    {
        // Fix simple cases
        $content = preg_replace('@(^|\s)"([^"]+)"@im',
            "$1".Fixer::LAQUO.Fixer::NO_BREAK_SPACE."$2".Fixer::NO_BREAK_SPACE.Fixer::RAQUO,
            $content);

        // Fix complex siblings cases
        if ($state_bag) {
            $content = $this->fixViaState($content, $state_bag);
        }

        return $content;
    }

    private function fixViaState($content, StateBag $state_bag)
    {
        $stored_sibling = $state_bag->getSiblingNode('FrenchQuotesOpenSolo');

        // If no stored open quote node & open quote detected
        if ($stored_sibling === false && preg_match('@(^|\s)"([^"]*)$@im', $content)) {
            // Store the current node
            $state_bag->storeSiblingNode('FrenchQuotesOpenSolo');

        // If we have a open sibling and we detect a closing quote
        } elseif ($stored_sibling instanceof StateNode && preg_match('@(^|[^"]+)"@im', $content)) {
            // Replace the closing tag
            $content = preg_replace('@(^|[^"]+)"(.+)@im', "$1".Fixer::NO_BREAK_SPACE.Fixer::RAQUO.'$2', $content);

            // Replace the opening tag
            $open_content = preg_replace('@(^|\s)"([^"]*)$@', "$1".Fixer::LAQUO.Fixer::NO_BREAK_SPACE.'$2', $stored_sibling->getNode()->wholeText);

            $state_bag->fixSiblingNode('FrenchQuotesOpenSolo', $open_content);
        }

        return $content;
    }
}

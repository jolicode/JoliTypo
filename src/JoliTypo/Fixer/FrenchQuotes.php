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

        if ($stored_sibling === false &&
            preg_match('@(^|\s)"([^"]+)$@', $content)) {

            $state_bag->storeSiblingNode('FrenchQuotesOpenSolo');
            var_dump($state_bag->getSiblingNode('FrenchQuotesOpenSolo'), $state_bag->getSiblingNode('FrenchQuotesOpenSolo')->getNode()->wholeText);
        } elseif ($stored_sibling instanceof StateNode &&
            preg_match('@(^|[^"]+)"\s@im', $content)) {

            $state_bag->destroySiblingNode('FrenchQuotesOpenSolo');

            // Replace the closing tag
            $content = preg_replace('@(^|[^"]+)"\s@im',
                        "$1".Fixer::NO_BREAK_SPACE.Fixer::RAQUO.' ',
                        $content);

            // Replace the opening tag

            $open_content = $stored_sibling->getNode()->wholeText;
            $open_content = preg_replace('@(^|\s)"([^"]+)$@',
                                    "$1".Fixer::LAQUO.Fixer::NO_BREAK_SPACE.'$2',
                $open_content);

            $stored_sibling->getParent()->replaceChild($stored_sibling->getDocument()->createTextNode($open_content), $stored_sibling->getNode());


        }

        //var_dump($state_bag);

        return $content;
    }
}

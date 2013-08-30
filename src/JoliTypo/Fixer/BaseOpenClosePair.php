<?php

namespace JoliTypo\Fixer;

use JoliTypo\StateBag;
use JoliTypo\StateNode;

/**
 * This class allow to fix two sibling Text node, even if they are separated by other nodes
 */
abstract class BaseOpenClosePair
{
    protected function fixViaState($content, StateBag $state_bag, $state_name, $open_regexp, $close_regexp, $open_replacement, $close_replacement)
    {
        $stored_sibling = $state_bag->getSiblingNode($state_name);

        // If no stored open quote node & open quote detected
        if ($stored_sibling === false && preg_match($open_regexp, $content)) {
            // Store the current node
            $state_bag->storeSiblingNode($state_name);

        // If we have a open sibling and we detect a closing quote
        } elseif ($stored_sibling instanceof StateNode && preg_match($close_regexp, $content)) {
            // Replace the closing tag
            $content = preg_replace($close_regexp, "$1".$close_replacement.'$2', $content, 1);

            // Replace the opening tag
            $open_content = preg_replace($open_regexp, "$1".$open_replacement.'$2', $stored_sibling->getNode()->wholeText, 1);

            $state_bag->fixSiblingNode($state_name, $open_content);
        }

        return $content;
    }
}

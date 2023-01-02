<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo\Fixer;

use JoliTypo\StateBag;
use JoliTypo\StateNode;

/**
 * This class allow to fix two sibling Text node, even if they are separated by other nodes.
 */
abstract class BaseOpenClosePair
{
    /**
     * @return string
     */
    protected function fixViaState(string $content, StateBag $stateBag, string $stateName, string $openRegexp, string $closeRegexp, string $openReplacement, string $closeReplacement)
    {
        $storedSibling = $stateBag->getSiblingNode($stateName);

        // If no stored open quote node & open quote detected
        if (null === $storedSibling && preg_match($openRegexp, $content)) {
            // Store the current node
            $stateBag->storeSiblingNode($stateName);

        // If we have a open sibling and we detect a closing quote
        } elseif ($storedSibling instanceof StateNode && preg_match($closeRegexp, $content)) {
            // Replace the closing tag
            $content = preg_replace($closeRegexp, '$1' . $closeReplacement . '$2', $content, 1);

            // Replace the opening tag
            $open_content = preg_replace($openRegexp, '$1' . $openReplacement . '$2', $storedSibling->getNode()->wholeText, 1);

            $stateBag->fixSiblingNode($stateName, $open_content);
        }

        return $content;
    }
}

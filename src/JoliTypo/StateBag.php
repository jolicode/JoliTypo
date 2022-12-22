<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo;

class StateBag
{
    protected int $currentDepth = 0;

    protected StateNode $currentNode;

    /**
     * @var array<StateNode>
     */
    protected array $siblingNode = [];

    /**
     * Save the current StateNode, edit MAY be done to it later.
     */
    public function storeSiblingNode(string $key): void
    {
        $this->siblingNode[$key][$this->currentDepth] = $this->currentNode;
    }

    public function getSiblingNode(string $key): ?StateNode
    {
        return $this->siblingNode[$key][$this->currentDepth] ?? null;
    }

    /**
     * Replace and destroy the content of a stored Node.
     */
    public function fixSiblingNode(string $key, string $new_content): void
    {
        $storedSibling = $this->getSiblingNode($key);

        if ($storedSibling) {
            $storedSibling->getParent()->replaceChild($storedSibling->getDocument()->createTextNode($new_content), $storedSibling->getNode());
            unset($this->siblingNode[$key][$this->currentDepth]);
        }
    }

    public function setCurrentNode(StateNode $currentNode): void
    {
        $this->currentNode = $currentNode;
    }

    public function setCurrentDepth(int $currentDepth): void
    {
        $this->currentDepth = $currentDepth;
    }

    public function getCurrentDepth(): int
    {
        return $this->currentDepth;
    }
}

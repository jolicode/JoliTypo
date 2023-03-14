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
    /**
     * @var int
     */
    protected $currentDepth = 0;

    /**
     * @var StateNode
     */
    protected $currentNode;

    /**
     * @var array<StateNode>
     */
    protected $siblingNode = [];

    /**
     * Save the current StateNode, edit MAY be done to it later.
     */
    public function storeSiblingNode(string $key)
    {
        $this->siblingNode[$key][$this->currentDepth] = $this->currentNode;
    }

    /**
     * @return StateNode|null
     */
    public function getSiblingNode(string $key)
    {
        return $this->siblingNode[$key][$this->currentDepth] ?? null;
    }

    /**
     * Replace and destroy the content of a stored Node.
     */
    public function fixSiblingNode(string $key, string $new_content)
    {
        $storedSibling = $this->getSiblingNode($key);

        if ($storedSibling) {
            $storedSibling->getParent()->replaceChild($storedSibling->getDocument()->createTextNode($new_content), $storedSibling->getNode());
            unset($this->siblingNode[$key][$this->currentDepth]);
        }
    }

    public function setCurrentNode(StateNode $currentNode)
    {
        $this->currentNode = $currentNode;
    }

    public function setCurrentDepth(int $currentDepth)
    {
        $this->currentDepth = $currentDepth;
    }

    /**
     * @return int
     */
    public function getCurrentDepth()
    {
        return $this->currentDepth;
    }
}

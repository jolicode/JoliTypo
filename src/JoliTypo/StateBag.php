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
     *
     * @param string $key
     */
    public function storeSiblingNode($key)
    {
        $this->siblingNode[$key][$this->currentDepth] = $this->currentNode;
    }

    /**
     * @param string $key
     *
     * @return bool|StateNode
     */
    public function getSiblingNode($key)
    {
        return isset($this->siblingNode[$key][$this->currentDepth]) ? $this->siblingNode[$key][$this->currentDepth] : false;
    }

    /**
     * Replace and destroy the content of a stored Node.
     *
     * @param string $key
     * @param string $new_content
     */
    public function fixSiblingNode($key, $new_content)
    {
        $storedSibling = $this->getSiblingNode($key);

        if ($storedSibling) {
            $storedSibling->getParent()->replaceChild($storedSibling->getDocument()->createTextNode($new_content), $storedSibling->getNode());
            unset($this->siblingNode[$key][$this->currentDepth]);
        }
    }

    /**
     * @param \JoliTypo\StateNode $currentNode
     */
    public function setCurrentNode(StateNode $currentNode)
    {
        $this->currentNode = $currentNode;
    }

    /**
     * @param int $currentDepth
     */
    public function setCurrentDepth($currentDepth)
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

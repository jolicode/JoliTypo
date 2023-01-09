<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo;

/**
 * @internal
 */
class StateNode
{
    private \DOMText $node;
    private \DOMNode $parent;
    private \DOMDocument $document;

    public function __construct(\DOMText $node, \DOMNode $parent, \DOMDocument $document)
    {
        $this->node = $node;
        $this->parent = $parent;
        $this->document = $document;
    }

    public function getNode(): \DOMText
    {
        return $this->node;
    }

    public function getParent(): \DOMNode
    {
        return $this->parent;
    }

    public function getDocument(): \DOMDocument
    {
        return $this->document;
    }

    public function replaceNode(\DOMText $node): void
    {
        $this->node = $node;
    }
}

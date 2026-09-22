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
    public function __construct(
        private \DOMText $node,
        private readonly \DOMNode $parent,
        private readonly \DOMDocument $document,
    ) {
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

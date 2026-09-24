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
        public private(set) \DOMText $node,
        public readonly \DOMNode $parent,
        public readonly \DOMDocument $document,
    ) {
    }

    public function replaceNode(\DOMText $node): void
    {
        $this->node = $node;
    }
}

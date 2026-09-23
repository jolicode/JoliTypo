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
        public private(set) \Dom\Text $node,
        public readonly \Dom\Node $parent,
        public readonly \Dom\Document $document,
    ) {
    }

    public function replaceNode(\Dom\Text $node): void
    {
        $this->node = $node;
    }
}

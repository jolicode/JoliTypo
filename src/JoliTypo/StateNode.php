<?php
namespace JoliTypo;

class StateNode
{
    /**
     * @var \DOMText
     */
    private $node;

    /**
     * @var \DOMNode
     */
    private $parent;

    /**
     * @var \DOMDocument
     */
    private $document;

    public function __construct(\DOMText $node, \DOMNode $parent, \DOMDocument $document)
    {
        $this->node     = $node;
        $this->parent   = $parent;
        $this->document = $document;
    }

    /**
     * @return \DOMText
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * @return \DOMNode
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return \DOMDocument
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * @param \DOMText $node
     */
    public function replaceNode($node)
    {
        $this->node = $node;
    }
}

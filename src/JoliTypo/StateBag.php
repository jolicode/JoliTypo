<?php
namespace JoliTypo;

class StateBag
{
    /**
     * @var int
     */
    protected $current_depth = 0;

    /**
     * @var StateNode
     */
    protected $current_node;

    /**
     * @var array<StateNode>
     */
    protected $sibling_node = array();

    /**
     * Save the current StateNode, edit MAY be done to it later
     *
     * @param string $key
     */
    public function storeSiblingNode($key)
    {
        $this->sibling_node[$key][$this->current_depth] = $this->current_node;
    }

    /**
     * @param  string         $key
     * @return bool|StateNode
     */
    public function getSiblingNode($key)
    {
        return isset($this->sibling_node[$key][$this->current_depth]) ? $this->sibling_node[$key][$this->current_depth] : false;
    }

    /**
     * Replace and destroy the content of a stored Node
     *
     * @param string $key
     * @param string $new_content
     */
    public function fixSiblingNode($key, $new_content)
    {
        $stored_sibling = $this->getSiblingNode($key);

        if ($stored_sibling) {
            $stored_sibling->getParent()->replaceChild($stored_sibling->getDocument()->createTextNode($new_content), $stored_sibling->getNode());
            unset($this->sibling_node[$key][$this->current_depth]);
        }
    }

    /**
     * @param \JoliTypo\StateNode $current_node
     */
    public function setCurrentNode(StateNode $current_node)
    {
        $this->current_node = $current_node;
    }

    /**
     * @param int $current_depth
     */
    public function setCurrentDepth($current_depth)
    {
        $this->current_depth = $current_depth;
    }

    /**
     * @return int
     */
    public function getCurrentDepth()
    {
        return $this->current_depth;
    }
}

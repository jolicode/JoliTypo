<?php
namespace JoliTypo;

use JoliTypo\Exception\BadRuleSetException;
use JoliTypo\Exception\InvalidMarkupException;

class StateBag
{
//    public $states = array(
//        'FrenchQuotesOpenSolo' => false
//    );

    public $current_depth = 0;
    public $current_node;

    protected $sibling_node = array();

    public function storeSiblingNode($key)
    {
        $this->sibling_node[$key][$this->current_depth] = $this->current_node;
    }

    public function getSiblingNode($key)
    {
        return isset($this->sibling_node[$key][$this->current_depth]) ? $this->sibling_node[$key][$this->current_depth] : false;
    }

    public function destroySiblingNode($key)
    {
        unset($this->sibling_node[$key][$this->current_depth]);
    }
}

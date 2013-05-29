<?php
namespace JoliTypo;

use JoliTypo\Exception\BadRuleSetException;
use JoliTypo\Exception\InvalidMarkupException;

class Fixer
{
    /**
     * DOMDocument does not like all the HTML entities; sometimes they are double encoded.
     * So the entities here are plain utf8 and DOCDocument::saveHTML transform them to entity.
     */
    const NO_BREAK_THIN_SPACE = "\xE2\x80\xAF"; // &#8239;
    const NO_BREAK_SPACE      = "\xC2\xA0"; // &#160;
    const ELLIPSIS            = "…";
    const LAQUO               = "«";
    const RAQUO               = "»";
    const RSQUO               = "’";
    const TIMES               = "×"; // &times;
    const NDASH               = "–"; // &ndash; or &#x2013;
    const MDASH               = "—"; // &mdash; or &#x2014;
    const LDQUO               = "“"; // &ldquo; or &#8220;
    const RDQUO               = "”"; // &rdquo; or &#8221;
    const SHY                 = "\xC2\xAD"; // &shy;

    /**
     * @var array   HTML Tags to bypass
     * @todo        Allow to set this in a YML file?
     */
    protected $protected_tags = array('head', 'link', 'pre', 'code', 'script', 'style');

    /**
     * @var array   Default rules by culture code
     * @todo        Allow to set this in a YML file?
     */
    protected $rule_sets = array(
        'fr_FR' => array('Ellipsis', 'Dimension', 'Dash', 'FrenchQuotes', 'FrenchNoBreakSpace', 'SingleQuote', 'Hyphen'),
        'fr_CA' => array('Ellipsis', 'Dimension', 'Dash', 'FrenchQuotes', 'SingleQuote', 'Hyphen'),
        'en_GB' => array('Ellipsis', 'Dimension', 'Dash', 'EnglishQuotes', 'SingleQuote', 'Hyphen')
    );

    protected $locale = null;

    /**
     * @var array The rules Fixer instances to apply on each DOMText
     */
    protected $_rules = array();

    /**
     * @var StateBag
     */
    protected $state_bag;

    public function __construct($locale = 'en_GB')
    {
        $this->setLocale($locale);
    }

    /**
     * @param  string $content HTML content to fix
     * @return string Content fixed
     */
    public function fix($content)
    {
        // Force rule refresh if empty
        if (empty($this->_rules)) {
            $this->setLocale($this->locale);
        }

        // Get a clean new StateBag
        $this->state_bag = new StateBag();

        $dom = $this->loadDOMDocument($content);

        $this->processDOM($dom, $dom);

        $content = $this->exportDOMDocument($dom);

        return $content;
    }

    /**
     * Add and use a list of rules for a given locale
     *
     * @param  string                        $locale
     * @param  array                         $rules  Can be the $rules key (culture code) or a set of rule class names
     * @throws Exception\BadRuleSetException
     * @return void
     */
    public function setRules($locale, $rules)
    {
        $this->addRules($locale, $rules);
        $this->setLocale($locale);
    }

    /**
     * Add a list of rules for a locale
     *
     * @param $locale
     * @param $rules
     * @throws Exception\BadRuleSetException
     */
    public function addRules($locale, $rules)
    {
        if (!is_array($rules) || empty($rules)) {
            throw new BadRuleSetException();
        }

        $this->rule_sets[$locale] = $rules;
    }

    /**
     * Build the _rules array of Fixer
     *
     * @param                                $rules
     * @throws Exception\BadRuleSetException
     */
    private function compileRules($rules)
    {
        $this->_rules = array();
        foreach ($rules as $rule) {
            if (is_object($rule)) {
                $fixer = $rule;
                $classname = get_class($rule);
            } else {
                $classname = class_exists($rule) ? $rule : (class_exists('JoliTypo\\Fixer\\'.$rule) ? 'JoliTypo\\Fixer\\'.$rule : false);
                if (!$classname) {
                    throw new BadRuleSetException();
                }

                $fixer = new $classname($this->getLocale());
            }

            if (!$fixer instanceof FixerInterface) {
                throw new BadRuleSetException();
            }
            $this->_rules[$classname] = $fixer;
        }

        if (empty($this->_rules)) {
            throw new BadRuleSetException();
        }
    }

    /**
     * Loop over all the DOMNode recursively
     *
     * @param \DOMNode     $node
     * @param \DOMDocument $dom
     */
    private function processDOM(\DOMNode $node, \DOMDocument $dom)
    {
        if ($node->hasChildNodes()) {
            $nodes = array();
            foreach ($node->childNodes as $childNode) {
                if ($childNode instanceof \DOMElement && $childNode->tagName) {
                    if (in_array($childNode->tagName, $this->protected_tags)) {
                        continue;
                    }
                }

                $nodes[] = $childNode;
            }

            $depth = $this->state_bag->getCurrentDepth();

            foreach ($nodes as $childNode) {
                if ($childNode instanceof \DOMText && !$childNode->isWhitespaceInElementContent()) {
                    $this->state_bag->setCurrentDepth($depth);
                    $this->doFix($childNode, $node, $dom);
                } else {
                    $this->state_bag->setCurrentDepth($this->state_bag->getCurrentDepth() + 1);
                    $this->processDOM($childNode, $dom);
                }
            }
        }
    }

    /**
     * Run the Fixers on a DOMText content
     *
     * @param \DOMText     $childNode The node to fix
     * @param \DOMNode     $node      The parent node where to replace the current one
     * @param \DOMDocument $dom       The Document
     */
    private function doFix(\DOMText $childNode, \DOMNode $node, \DOMDocument $dom)
    {
        $content        = $childNode->wholeText;
        $current_node   = new StateNode($childNode, $node, $dom);

        $this->state_bag->setCurrentNode($current_node);

        // run the string on all the fixers
        foreach ($this->_rules as $fixer) {
            $content = $fixer->fix($content, $this->state_bag);
        }

        // update the DOM only if the node has changed
        if ($childNode->wholeText !== $content) {
            $new_node = $dom->createTextNode($content);
            $node->replaceChild($new_node, $childNode);

            // As the node is replaced, we also update it in the StateNode
            $current_node->replaceNode($new_node);
        }
    }

    /**
     * @param $content
     * @return \DOMDocument
     * @throws Exception\InvalidMarkupException
     */
    private function loadDOMDocument($content)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->encoding = "UTF-8";

        $dom->strictErrorChecking   = false;
        $dom->substituteEntities    = false;
        $dom->formatOutput          = false;

        // Little hack to force UTF-8
        if (strpos($content, '<?xml encoding') === false) {
            $hack = strpos($content, '<body') === false ? '<?xml encoding="UTF-8"><body>' : '<?xml encoding="UTF-8">';
            $loaded = $dom->loadHTML($hack . $content);
        } else {
            $loaded = $dom->loadHTML($content);
        }

        if (!$loaded) {
            throw new InvalidMarkupException("Can't load the given HTML");
        }

        foreach ($dom->childNodes as $item) {
          if ($item->nodeType === XML_PI_NODE) {
            $dom->removeChild($item); // remove encoding hack
            break;
          }
        }

        return $dom;
    }

    /**
     * @param  \DOMDocument $dom
     * @return string
     */
    private function exportDOMDocument(\DOMDocument $dom)
    {
        // Remove added body & doctype
        $content = preg_replace(array(
                "/^\<\!DOCTYPE.*?<html><body>/si",
                "!</body></html>$!si"),
                "", $dom->saveHTML());

        return trim($content);
    }

    /**
     * @param  array                     $protected_tags
     * @throws \InvalidArgumentException
     */
    public function setProtectedTags($protected_tags)
    {
        if (!is_array($protected_tags)) {
            throw new \InvalidArgumentException("Protected tags must be an array (empty array for no protection).");
        }

        $this->protected_tags = $protected_tags;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function setLocale($locale)
    {
        if (!is_string($locale) || !isset($this->rule_sets[$locale])) {
            throw new BadRuleSetException();
        }

        $this->locale = $locale;
        $this->compileRules($this->rule_sets[$locale]);
    }
}

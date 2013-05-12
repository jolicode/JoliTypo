<?php
namespace JoliTypo;

use JoliTypo\Exception\BadRuleSetException;
use JoliTypo\Exception\InvalidMarkupException;

class Fixer
{
    /**
     * DOMDocument does not like all the HTML entities; sometimes they are double encoded.
     * So the entities here are encoded and DOCDocument::saveHTML transform them to entity.
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

    /**
     * @var array   HTML Tags to bypass
     * @todo        Allow to set this in a YML file?
     */
    protected $protected_tags = array('pre', 'code', 'script', 'style');

    /**
     * @var array   Default rules by culture code
     * @todo        Allow to set this in a YML file?
     */
    protected $rule_sets = array(
        'fr_FR' => array('Ellipsis', 'Dimension', 'Dash', 'FrenchQuotes', 'FrenchNoBreakSpace', 'SingleQuote'),
        'fr_CA' => array('Ellipsis', 'Dimension', 'Dash', 'FrenchQuotes', 'SingleQuote'),
        'en_GB' => array('Ellipsis', 'Dimension', 'Dash', 'EnglishQuotes', 'SingleQuote')
    );

    /**
     * @var The rules to apply on each DOMText
     */
    protected $_rules;

    public function __construct($rule = 'fr_FR')
    {
        $this->setRules($rule);
    }

    /**
     * @param  string $content HTML content to fix
     * @return string Content fixed
     */
    public function fix($content)
    {
        $dom = $this->loadDOMDocument($content);

        $this->processDOM($dom, $dom);

        $content = $this->exportDOMDocument($dom);

        return $content;
    }

    /**
     * @param  string|array                  $rule Can be the $rules key (culture code) or a set of rule class names
     * @throws Exception\BadRuleSetException
     *
     * @todo Allow to specify a simple lang code like "en" or "fr" instead of a full locale code.
     */
    public function setRules($rule)
    {
        if (is_array($rule) && !empty($rule)) {
            $this->_rules = $rule;
        } elseif (is_string($rule) && isset($this->rule_sets[$rule])) {
            $this->_rules = $this->rule_sets[$rule];
        } else {
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

            foreach ($nodes as $childNode) {
                if ($childNode instanceof \DOMText && !$childNode->isWhitespaceInElementContent()) {
                    $this->doFix($childNode, $node, $dom);
                } else {
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
        $content = $childNode->wholeText;

        // @todo Do not instantiate new Fixer, store them for later reuse.
        foreach ($this->_rules as $fixer_name) {
            $class = 'JoliTypo\\Fixer\\'.$fixer_name;
            $fixer = new $class();

            $content = $fixer->fix($content);
        }

        if ($childNode->wholeText !== $content) {
            $node->replaceChild($dom->createTextNode($content), $childNode);
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
        $loaded = $dom->loadHTML('<?xml encoding="UTF-8"><body>' . $content);

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
        $content = preg_replace(array("/^\<\!DOCTYPE.*?<html><body>/si",
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
}

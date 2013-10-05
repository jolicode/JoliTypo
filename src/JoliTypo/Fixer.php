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
    const LAQUO               = "«"; // &laquo;
    const RAQUO               = "»"; // &raquo;
    const RSQUO               = "’"; // &rsquo;
    const TIMES               = "×"; // &times;
    const NDASH               = "–"; // &ndash; or &#x2013;
    const MDASH               = "—"; // &mdash; or &#x2014;
    const LDQUO               = "“"; // &ldquo; or &#8220;
    const RDQUO               = "”"; // &rdquo; or &#8221;
    const BDQUO               = "„"; // &bdquo; or &#8222;
    const SHY                 = "\xC2\xAD"; // &shy;
    const TRADE               = "™"; // &trade;
    const REG                 = "®"; // &reg;
    const COPY                = "©"; // &copy;

    /**
     * @var array   HTML Tags to bypass
     */
    protected $protected_tags = array('head', 'link', 'pre', 'code', 'script', 'style');

    /**
     * @var string  The default locale (used by some Fixer)
     */
    protected $locale = "en_GB";

    /**
     * @var array The rules Fixer instances to apply on each DOMText
     */
    protected $_rules = array();

    /**
     * @var StateBag
     */
    protected $state_bag;

    /**
     * @param array $rules Array of Fixer
     */
    public function __construct($rules)
    {
        $this->compileRules($rules);
    }

    /**
     * @param  string $content  HTML content to fix
     * @throws Exception\BadRuleSetException
     * @return string           Fixed content
     */
    public function fix($content)
    {
        // Get a clean new StateBag
        $this->state_bag = new StateBag();

        $dom = $this->loadDOMDocument($content);

        $this->processDOM($dom, $dom);

        $content = $this->exportDOMDocument($dom);

        return $content;
    }

    /**
     * Change the list of rules for a given locale
     *
     * @param  array                         $rules  Array of Fixer
     * @throws Exception\BadRuleSetException
     * @return void
     */
    public function setRules($rules)
    {
        $this->compileRules($rules);
    }

    /**
     * Build the _rules array of Fixer
     *
     * @param                                $rules
     * @throws Exception\BadRuleSetException
     */
    private function compileRules($rules)
    {
        if (!is_array($rules) || empty($rules)) {
            throw new BadRuleSetException("Rules must be an array of Fixer");
        }

        $this->_rules = array();
        foreach ($rules as $rule) {
            if (is_object($rule)) {
                $fixer = $rule;
                $classname = get_class($rule);
            } else {
                $classname = class_exists($rule) ? $rule : (class_exists('JoliTypo\\Fixer\\'.$rule) ? 'JoliTypo\\Fixer\\'.$rule : false);
                if (!$classname) {
                    throw new BadRuleSetException(sprintf("Fixer %s not found", $rule));
                }

                $fixer = new $classname($this->getLocale());
            }

            if (!$fixer instanceof FixerInterface) {
                throw new BadRuleSetException(sprintf("%s must implement FixerInterface", $classname));
            }

            $this->_rules[$classname] = $fixer;
        }

        if (empty($this->_rules)) {
            throw new BadRuleSetException("No rules configured, can't fix the content!");
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

        $libxml_current = libxml_use_internal_errors(true);

        // Little hack to force UTF-8
        if (strpos($content, '<?xml encoding') === false) {
            $hack = strpos($content, '<body') === false ? '<?xml encoding="UTF-8"><body>' : '<?xml encoding="UTF-8">';
            $loaded = $dom->loadHTML($hack . $content);
        } else {
            $loaded = $dom->loadHTML($content);
        }

        libxml_use_internal_errors($libxml_current);

        if (!$loaded) {
            throw new InvalidMarkupException("Can't load the given HTML via DomDocument");
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
     * @param \DOMDocument  $dom
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
     * Customize the list of protected tags
     *
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

    /**
     * Get the current Locale tag
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Change the locale of the Fixer
     *
     * @param  string   $locale     An IETF language tag
     * @throws \InvalidArgumentException
     */
    public function setLocale($locale)
    {
        if (!is_string($locale) || empty($locale)) {
            throw new \InvalidArgumentException("Locale must be an IETF language tag.");
        }

        // Set the Locale on Fixer that needs it
        foreach ($this->_rules as $rule) {
            if ($rule instanceof LocaleAwareFixerInterface) {
                $rule->setLocale($locale);
            }
        }

        $this->locale = $locale;
    }

    /**
     * Get language part of a Locale string (fr_FR => fr)
     *
     * @param $locale
     * @return string
     */
    public static function getLanguageFromLocale($locale)
    {
        if (strpos($locale, '_')) {
            $parts = explode('_', $locale);

            return strtolower($parts[0]);
        }

        return $locale;
    }
}

<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo;

use JoliTypo\Exception\BadRuleSetException;
use JoliTypo\Exception\InvalidMarkupException;

class Fixer
{
    /**
     * DOMDocument does not like all the HTML entities; sometimes they are double encoded.
     * So the entities here are plain utf8 and DOCDocument::saveHTML transform them to entity.
     */
    public const NO_BREAK_THIN_SPACE = "\xE2\x80\xAF"; // &#8239;
    public const NO_BREAK_SPACE = "\xC2\xA0"; // &#160;
    public const ELLIPSIS = '…';
    public const LAQUO = '«'; // &laquo;
    public const RAQUO = '»'; // &raquo;
    public const RSQUO = '’'; // &rsquo;
    public const TIMES = '×'; // &times;
    public const NDASH = '–'; // &ndash; or &#x2013;
    public const MDASH = '—'; // &mdash; or &#x2014;
    public const LDQUO = '“'; // &ldquo; or &#8220;
    public const RDQUO = '”'; // &rdquo; or &#8221;
    public const BDQUO = '„'; // &bdquo; or &#8222;
    public const SHY = "\xC2\xAD"; // &shy;
    public const TRADE = '™'; // &trade;
    public const REG = '®'; // &reg;
    public const COPY = '©'; // &copy;
    public const ALL_SPACES = "\xE2\x80\xAF|\xC2\xAD|\xC2\xA0|\\s"; // All supported spaces, used in regexps. Better than \s

    public const RECOMMENDED_RULES_BY_LOCALE = [
        'en_GB' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],
        'fr_FR' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'FrenchNoBreakSpace', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],
        'fr_CA' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],
        'de_DE' => ['Ellipsis', 'Dimension', 'Unit', 'Dash', 'SmartQuotes', 'NoSpaceBeforeComma', 'CurlyQuote', 'Hyphen', 'Trademark'],
    ];

    private array $protectedTags = ['head', 'link', 'pre', 'code', 'script', 'style'];

    private string $locale = 'en_GB';

    /**
     * @var array<FixerInterface> The rules Fixer instances to apply on each DOMText
     */
    private array $_rules = [];

    private ?StateBag $stateBag = null;

    /**
     * @param array $rules Array of Fixer
     */
    public function __construct(array $rules)
    {
        $this->compileRules($rules);
    }

    /**
     * @param string $content HTML content to fix
     *
     * @return string Fixed content
     */
    public function fix(string $content)
    {
        $trimmed = trim($content);
        if (empty($trimmed)) {
            return $content;
        }

        // Get a clean new StateBag
        $this->stateBag = new StateBag();

        $dom = $this->loadDOMDocument($trimmed);

        $this->processDOM($dom, $dom);

        return $this->exportDOMDocument($dom);
    }

    /**
     * @param string $content Basic content to fix
     *
     * @return string
     */
    public function fixString(string $content)
    {
        foreach ($this->_rules as $fixer) {
            $content = $fixer->fix($content, $this->stateBag);
        }

        return $content;
    }

    /**
     * Change the list of rules for a given locale.
     *
     * @throws BadRuleSetException
     */
    public function setRules(array $rules)
    {
        $this->compileRules($rules);
    }

    public function getProtectedTags(): array
    {
        return $this->protectedTags;
    }

    /**
     * Customize the list of protected tags.
     */
    public function setProtectedTags(array $protectedTags)
    {
        $this->protectedTags = $protectedTags;
    }

    /**
     * Get the current Locale tag.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Change the locale of the Fixer.
     *
     * @param string $locale An IETF language tag
     *
     * @throws \InvalidArgumentException
     */
    public function setLocale(string $locale)
    {
        if (!$locale) {
            throw new \InvalidArgumentException('Locale must be an IETF language tag.');
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
     * Get language part of a Locale string (fr_FR => fr).
     *
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

    protected function getStateBug(): StateBag
    {
        return $this->stateBag;
    }

    /**
     * Build the _rules array of Fixer.
     *
     * @throws BadRuleSetException
     */
    private function compileRules(array $rules): void
    {
        if (empty($rules)) {
            throw new BadRuleSetException('Rules must be an array of Fixer');
        }

        $this->_rules = [];
        foreach ($rules as $rule) {
            if (\is_object($rule)) {
                $fixer = $rule;
                $className = \get_class($rule);
            } else {
                $className = class_exists($rule) ? $rule : (class_exists(
                    'JoliTypo\\Fixer\\' . $rule
                ) ? 'JoliTypo\\Fixer\\' . $rule : false);
                if (!$className) {
                    throw new BadRuleSetException(sprintf('Fixer %s not found', $rule));
                }

                $fixer = new $className($this->getLocale());
            }

            if (!$fixer instanceof FixerInterface) {
                throw new BadRuleSetException(sprintf('%s must implement FixerInterface', $className));
            }

            $this->_rules[$className] = $fixer;
        }

        if (empty($this->_rules)) {
            throw new BadRuleSetException("No rules configured, can't fix the content!");
        }
    }

    /**
     * Loop over all the DOMNode recursively.
     */
    private function processDOM(\DOMNode $node, \DOMDocument $dom): void
    {
        if ($node->hasChildNodes()) {
            $nodes = [];
            foreach ($node->childNodes as $childNode) {
                if ($childNode instanceof \DOMElement && $childNode->tagName) {
                    if (\in_array($childNode->tagName, $this->protectedTags)) {
                        continue;
                    }
                }

                $nodes[] = $childNode;
            }

            $depth = $this->stateBag->getCurrentDepth();

            foreach ($nodes as $childNode) {
                if ($childNode instanceof \DOMText && !$childNode->isWhitespaceInElementContent()) {
                    $this->stateBag->setCurrentDepth($depth);
                    $this->doFix($childNode, $node, $dom);
                } else {
                    $this->stateBag->setCurrentDepth($this->stateBag->getCurrentDepth() + 1);
                    $this->processDOM($childNode, $dom);
                }
            }
        }
    }

    /**
     * Run the Fixers on a DOMText content.
     *
     * @param \DOMText     $childNode The node to fix
     * @param \DOMNode     $node      The parent node where to replace the current one
     * @param \DOMDocument $dom       The Document
     */
    private function doFix(\DOMText $childNode, \DOMNode $node, \DOMDocument $dom): void
    {
        $content = $childNode->wholeText;
        $current_node = new StateNode($childNode, $node, $dom);

        $this->stateBag->setCurrentNode($current_node);

        // run the string on all the fixers
        foreach ($this->_rules as $fixer) {
            $content = $fixer->fix($content, $this->stateBag);
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
     * @throws Exception\InvalidMarkupException
     */
    private function loadDOMDocument($content): \DOMDocument
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->encoding = 'UTF-8';

        $dom->strictErrorChecking = false;
        $dom->substituteEntities = false;
        $dom->formatOutput = false;

        // Change libxml config
        $libxmlCurrent = libxml_use_internal_errors(true);

        $loaded = $dom->loadHTML($this->fixContentEncoding($content));

        // Restore libxml config
        libxml_use_internal_errors($libxmlCurrent);

        if (!$loaded) {
            throw new InvalidMarkupException("Can't load the given HTML via DomDocument");
        }

        return $dom;
    }

    /**
     * Convert the content encoding properly and add Content-Type meta if HTML document.
     *
     * @see http://php.net/manual/en/domdocument.loadhtml.php#91513
     * @see https://github.com/jolicode/JoliTypo/issues/7
     */
    private function fixContentEncoding($content): string
    {
        if (!empty($content)) {
            // Little hack to force UTF-8
            if (false === strpos($content, '<?xml encoding')) {
                $hack = false === strpos(
                    $content,
                    '<body'
                ) ? '<?xml encoding="UTF-8"><body>' : '<?xml encoding="UTF-8">';
                $content = $hack . $content;
            }

            $encoding = null;
            foreach (['UTF-8', 'ASCII', 'ISO-8859-1', 'windows-1252', 'iso-8859-15'] as $testedEncoding) {
                if (mb_detect_encoding($content, $testedEncoding, true)) {
                    $encoding = $testedEncoding;

                    break;
                }
            }

            $headPos = mb_strpos($content, '<head>');

            // Add a meta to the <head> section
            if (false !== $headPos) {
                $headPos += 6;
                $content = mb_substr($content, 0, $headPos) .
                    '<meta http-equiv="Content-Type" content="text/html; charset=' . $encoding . '">' .
                    mb_substr($content, $headPos);
            }

            if ('UTF-8' !== $encoding) {
                $content = mb_convert_encoding($content, 'UTF-8', $encoding);
            }
        }

        return $content;
    }

    private function exportDOMDocument(\DOMDocument $dom): string
    {
        // Remove added body & doctype
        $content = preg_replace(
            [
                '/^\\<\\!DOCTYPE.*?<html>.*?<body>/si',
                '!</body>\n?</html>$!si',
            ],
            '',
            $dom->saveHTML()
        );

        return trim($content);
    }
}

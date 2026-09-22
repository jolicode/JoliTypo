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
    public const string NO_BREAK_THIN_SPACE = "\xE2\x80\xAF"; // &#8239;
    public const string NO_BREAK_SPACE = "\xC2\xA0"; // &#160;
    public const string ELLIPSIS = '…';
    public const string LAQUO = '«'; // &laquo;
    public const string RAQUO = '»'; // &raquo;
    public const string RSQUO = '’'; // &rsquo;
    public const string TIMES = '×'; // &times;
    public const string NDASH = '–'; // &ndash; or &#x2013;
    public const string MDASH = '—'; // &mdash; or &#x2014;
    public const string LDQUO = '“'; // &ldquo; or &#8220;
    public const string RDQUO = '”'; // &rdquo; or &#8221;
    public const string BDQUO = '„'; // &bdquo; or &#8222;
    public const string SHY = "\xC2\xAD"; // &shy;
    public const string TRADE = '™'; // &trade;
    public const string REG = '®'; // &reg;
    public const string COPY = '©'; // &copy;
    public const string ALL_SPACES = "\xE2\x80\xAF|\xC2\xAD|\xC2\xA0|\\h"; // All supported spaces, used in regexps. \h matches horizontal spaces (tabs, thin spaces, nbsp, ...) but never line breaks

    /**
     * @deprecated since 1.7.0, use LocaleConfig::RECOMMENDED_RULES_BY_LOCALE instead
     * @see LocaleConfig::RECOMMENDED_RULES_BY_LOCALE
     */
    public const array RECOMMENDED_RULES_BY_LOCALE = LocaleConfig::RECOMMENDED_RULES_BY_LOCALE;

    /**
     * @var list<string>
     */
    private array $protectedTags = ['head', 'link', 'pre', 'code', 'script', 'style'];

    private string $locale = 'en_GB';

    /**
     * @var array<string, FixerInterface> The rules Fixer instances to apply on each DOMText, indexed by class name
     */
    private array $rules = [];

    private ?StateBag $stateBag = null;

    /**
     * @param array<FixerInterface|string> $rules Fixer instances, fully qualified class names or built-in fixer names
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
    public function fix(string $content): string
    {
        $trimmed = trim($content);
        if (empty($trimmed)) {
            return $content;
        }

        // Get a clean new StateBag
        $stateBag = $this->stateBag = new StateBag();

        $dom = $this->loadDOMDocument($trimmed);

        $this->processDOM($dom, $dom, $stateBag);

        return $this->exportDOMDocument($dom);
    }

    /**
     * Run the fixers directly on a string, without any HTML parsing.
     *
     * @param string $content Basic content to fix
     */
    public function fixString(string $content): string
    {
        foreach ($this->rules as $fixer) {
            $content = $fixer->fix($content, $this->stateBag);
        }

        return $content;
    }

    /**
     * Change the list of rules for a given locale.
     *
     * @param array<FixerInterface|string> $rules Fixer instances, fully qualified class names or built-in fixer names
     *
     * @throws BadRuleSetException
     */
    public function setRules(array $rules): void
    {
        $this->compileRules($rules);
    }

    /**
     * @return list<string>
     */
    public function getProtectedTags(): array
    {
        return $this->protectedTags;
    }

    /**
     * Customize the list of protected tags.
     *
     * @param list<string> $protectedTags
     */
    public function setProtectedTags(array $protectedTags): void
    {
        $this->protectedTags = $protectedTags;
    }

    /**
     * Get the current Locale tag.
     */
    public function getLocale(): string
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
    public function setLocale(string $locale): void
    {
        if (!$locale) {
            throw new \InvalidArgumentException('Locale must be an IETF language tag.');
        }

        // Set the Locale on Fixer that needs it
        foreach ($this->rules as $rule) {
            if ($rule instanceof LocaleAwareFixerInterface) {
                $rule->setLocale($locale);
            }
        }

        $this->locale = $locale;
    }

    /**
     * Get language part of a Locale string (fr_FR => fr).
     */
    public static function getLanguageFromLocale(string $locale): string
    {
        if (strpos($locale, '_')) {
            $parts = explode('_', $locale);

            return strtolower($parts[0]);
        }

        return $locale;
    }

    protected function getStateBug(): ?StateBag
    {
        return $this->stateBag;
    }

    /**
     * Build the rules array of Fixer.
     *
     * @param array<FixerInterface|string> $rules
     *
     * @throws BadRuleSetException
     */
    private function compileRules(array $rules): void
    {
        if (empty($rules)) {
            throw new BadRuleSetException('Rules must be an array of Fixer');
        }

        $this->rules = [];
        foreach ($rules as $rule) {
            if (\is_object($rule)) {
                $fixer = $rule;
                $className = $rule::class;
            } else {
                $builtInClassName = 'JoliTypo\Fixer\\' . $rule;
                $className = match (true) {
                    class_exists($rule) => $rule,
                    class_exists($builtInClassName) => $builtInClassName,
                    default => throw new BadRuleSetException(\sprintf('Fixer %s not found', $rule)),
                };

                $fixer = new $className($this->getLocale());
            }

            if (!$fixer instanceof FixerInterface) {
                throw new BadRuleSetException(\sprintf('%s must implement FixerInterface', $className));
            }

            $this->rules[$className] = $fixer;
        }
    }

    /**
     * Loop over all the DOMNode recursively.
     */
    private function processDOM(\DOMNode $node, \DOMDocument $dom, StateBag $stateBag): void
    {
        if (!$node->hasChildNodes()) {
            return;
        }

        // Copy the list first, as fixing a node replaces it in the live child list
        $nodes = [];
        foreach ($node->childNodes as $childNode) {
            if ($childNode instanceof \DOMElement && \in_array($childNode->tagName, $this->protectedTags, true)) {
                continue;
            }

            $nodes[] = $childNode;
        }

        $depth = $stateBag->getCurrentDepth();

        foreach ($nodes as $childNode) {
            if ($childNode instanceof \DOMText && !$childNode->isWhitespaceInElementContent()) {
                $stateBag->setCurrentDepth($depth);
                $this->doFix($childNode, $node, $dom, $stateBag);
            } else {
                $stateBag->setCurrentDepth($stateBag->getCurrentDepth() + 1);
                $this->processDOM($childNode, $dom, $stateBag);
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
    private function doFix(\DOMText $childNode, \DOMNode $node, \DOMDocument $dom, StateBag $stateBag): void
    {
        $content = $childNode->wholeText;
        $currentNode = new StateNode($childNode, $node, $dom);

        $stateBag->setCurrentNode($currentNode);

        // run the string on all the fixers
        foreach ($this->rules as $fixer) {
            $content = $fixer->fix($content, $stateBag);
        }

        // update the DOM only if the node has changed
        if ($childNode->wholeText !== $content) {
            $newNode = $dom->createTextNode($content);
            $node->replaceChild($newNode, $childNode);

            // As the node is replaced, we also update it in the StateNode
            $currentNode->replaceNode($newNode);
        }
    }

    /**
     * @throws InvalidMarkupException
     */
    private function loadDOMDocument(string $content): \DOMDocument
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
    private function fixContentEncoding(string $content): string
    {
        // Little hack to force UTF-8
        if (!str_contains($content, '<?xml encoding')) {
            $hack = str_contains($content, '<body') ? '<?xml encoding="UTF-8">' : '<?xml encoding="UTF-8"><body>';
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
            $content = mb_substr($content, 0, $headPos)
                . '<meta http-equiv="Content-Type" content="text/html; charset=' . $encoding . '">'
                . mb_substr($content, $headPos);
        }

        if ('UTF-8' !== $encoding) {
            $content = (string) mb_convert_encoding($content, 'UTF-8', $encoding);
        }

        return $content;
    }

    private function exportDOMDocument(\DOMDocument $dom): string
    {
        // Remove added body & doctype
        $content = preg_replace(
            [
                '/^\<\!DOCTYPE.*?<html>.*?<body>/si',
                '!</body>\n?</html>$!si',
            ],
            '',
            (string) $dom->saveHTML()
        );

        return trim((string) $content);
    }
}

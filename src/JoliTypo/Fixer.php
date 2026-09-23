<?php

/*
 * This file is part of JoliTypo - a project by JoliCode.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace JoliTypo;

use JoliTypo\Exception\BadRuleSetException;

class Fixer
{
    /**
     * The fixers work on plain UTF-8 characters, never on HTML entities.
     * The HTML5 serializer only escapes &, <, > and the no-break space (as &nbsp;), everything else is output as UTF-8.
     */
    public const string NO_BREAK_THIN_SPACE = "\xE2\x80\xAF"; // &#8239;
    public const string NO_BREAK_SPACE = "\xC2\xA0"; // &#160;
    public const string ELLIPSIS = '…';
    public const string LAQUO = '«'; // &laquo;
    public const string RAQUO = '»'; // &raquo;
    public const string LSAQUO = '‹'; // &lsaquo; or &#8249;
    public const string RSAQUO = '›'; // &rsaquo; or &#8250;
    public const string RSQUO = '’'; // &rsquo;
    public const string LSQUO = '‘'; // &lsquo; or &#8216;
    public const string SBQUO = '‚'; // &sbquo; or &#8218;
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
    public const string ALL_SPACES_CLASS = "\xE2\x80\xAF\xC2\xAD\xC2\xA0\\h"; // The same spaces, without the alternation, to be embedded in a character class

    /**
     * @deprecated since 1.7.0, use LocaleConfig::RECOMMENDED_RULES_BY_LOCALE instead
     * @see LocaleConfig::RECOMMENDED_RULES_BY_LOCALE
     */
    #[\Deprecated(message: 'use LocaleConfig::RECOMMENDED_RULES_BY_LOCALE instead', since: '1.7.0')]
    public const array RECOMMENDED_RULES_BY_LOCALE = LocaleConfig::RECOMMENDED_RULES_BY_LOCALE;

    /**
     * @var list<string>
     */
    private array $protectedTags = ['head', 'link', 'pre', 'code', 'script', 'style'];

    private string $locale = 'en_GB';

    /**
     * @var array<string, FixerInterface> The rules Fixer instances to apply on each text node, indexed by class name
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
        $this->stateBag = new StateBag();

        $utf8 = $this->toUtf8($trimmed);

        return $this->isDocument($utf8) ? $this->fixDocument($utf8) : $this->fixFragment($utf8);
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

    protected function getStateBug(): StateBag
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
     * Fix a whole HTML document, returned with its doctype, <html>, <head> and <body>.
     */
    private function fixDocument(string $content): string
    {
        $document = \Dom\HTMLDocument::createFromString($content, \LIBXML_NOERROR, 'UTF-8');

        $this->processDOM($document, $document);

        return trim($document->saveHtml());
    }

    /**
     * Fix a fragment of HTML.
     *
     * It is parsed in the context of a <body> element, like a browser does with innerHTML,
     * so that <style>, <title> or leading text stay where they are.
     */
    private function fixFragment(string $content): string
    {
        $document = \Dom\HTMLDocument::createEmpty();
        $html = $document->createElement('html');
        $body = $document->createElement('body');
        $html->append($body);
        $document->append($html);
        $body->innerHTML = $content;

        $this->processDOM($body, $document);

        return trim($body->innerHTML);
    }

    /**
     * Loop over all the nodes recursively.
     */
    private function processDOM(\Dom\Node $node, \Dom\Document $document): void
    {
        if (!$node->hasChildNodes()) {
            return;
        }

        // Copy the list first, as fixing a node replaces it in the live child list
        $nodes = [];
        foreach ($node->childNodes as $childNode) {
            if ($childNode instanceof \Dom\Element && \in_array($childNode->localName, $this->protectedTags, true)) {
                continue;
            }

            $nodes[] = $childNode;
        }

        $depth = $this->stateBag->getCurrentDepth();

        foreach ($nodes as $childNode) {
            if ($childNode instanceof \Dom\Text && '' !== trim($childNode->data)) {
                $this->stateBag->setCurrentDepth($depth);
                $this->doFix($childNode, $node, $document);
            } else {
                $this->stateBag->setCurrentDepth($this->stateBag->getCurrentDepth() + 1);
                $this->processDOM($childNode, $document);
            }
        }
    }

    /**
     * Run the Fixers on a text node.
     *
     * @param \Dom\Text     $childNode The node to fix
     * @param \Dom\Node     $node      The parent node where to replace the current one
     * @param \Dom\Document $document  The Document
     */
    private function doFix(\Dom\Text $childNode, \Dom\Node $node, \Dom\Document $document): void
    {
        $content = $childNode->data;
        $currentNode = new StateNode($childNode, $node, $document);

        $this->stateBag->setCurrentNode($currentNode);

        // run the string on all the fixers
        foreach ($this->rules as $fixer) {
            $content = $fixer->fix($content, $this->stateBag);
        }

        // update the DOM only if the node has changed
        if ($childNode->data !== $content) {
            $newNode = $document->createTextNode($content);
            $node->replaceChild($newNode, $childNode);

            // As the node is replaced, we also update it in the StateNode
            $currentNode->replaceNode($newNode);
        }
    }

    /**
     * Guess whether the content is a whole HTML document or a fragment of one.
     */
    private function isDocument(string $content): bool
    {
        return (bool) preg_match('/<(?:!doctype|html)[\s>]/i', $content);
    }

    /**
     * Convert the content to UTF-8, the only encoding the HTML parser and the fixers work with.
     *
     * A leading XML declaration, once the way to tell libxml which encoding to read
     * (see https://github.com/jolicode/JoliTypo/issues/7), is still accepted and removed.
     */
    private function toUtf8(string $content): string
    {
        $content = (string) preg_replace('/^<\?xml\b[^>]*>\s*/i', '', $content, 1);

        $encoding = array_find(
            ['UTF-8', 'ASCII', 'ISO-8859-1', 'windows-1252', 'iso-8859-15'],
            static fn (string $testedEncoding): bool => false !== mb_detect_encoding($content, $testedEncoding, true)
        ) ?? 'UTF-8';

        if ('UTF-8' !== $encoding) {
            $content = (string) mb_convert_encoding($content, 'UTF-8', $encoding);
        }

        return $content;
    }
}

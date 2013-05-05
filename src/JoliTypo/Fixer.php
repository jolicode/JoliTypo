<?php
namespace JoliTypo;

use JoliTypo\Exception\InvalidMarkupException;

class Fixer
{
    /**
     * DOMDocument does not like all the HTML entities; sometimes they are double encoded.
     * So the entities here are encoded and DOCDocument::saveHTML transform them to entity.
     */
    const NO_BREAK_THIN_SPACE = " "; // &#8239;
    const NO_BREAK_SPACE      = " "; // &#160;
    const ELLIPSIS            = "…";
    const LAQUO               = "«";
    const RAQUO               = "»";

    protected $protected_tags = array('pre', 'code', 'script', 'style');

    public function fix($content)
    {
        $dom = $this->loadDOMDocument($content);

        $this->processDOM($dom, $dom);

        $content = $this->exportDOMDocument($dom);

        return $content;
    }

    /**
     * @param \DOMNode     $node
     * @param \DOMDocument $dom
     */
    private function processDOM(\DOMNode $node, \DOMDocument $dom) {
        if($node->hasChildNodes()) {
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
                }
                else {
                    $this->processDOM($childNode, $dom);
                }
            }
        }
    }


    private function doFix($childNode, $node, $dom)
    {
        $content = $childNode->wholeText;

        foreach (array('Ellipsis', 'FrenchQuotes', 'FrenchNoBreakSpace') as $fixer_name) {
            $class = 'JoliTypo\\Fixer\\'.$fixer_name;
            $fixer = new $class();

            $content = $fixer->fix($content);
        }

        //if ($childNode->wholeText !== $content) {
          //  var_dump($content);
            $node->replaceChild($dom->createTextNode($content), $childNode);
        //}
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

    private function exportDOMDocument(\DOMDocument $dom)
    {
        // Remove added body & doctype
        $content = preg_replace(array("/^\<\!DOCTYPE.*?<html><body>/si",
                                          "!</body></html>$!si"),
                                    "", $dom->saveHTML());
        return trim($content);
    }
}

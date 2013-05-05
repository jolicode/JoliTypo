<?php
namespace JoliTypo;

use JoliTypo\Exception\InvalidMarkupException;

class Fixer
{
    const NO_BREAK_THIN_SPACE = " "; // &#8239;
    const NO_BREAK_SPACE      = " "; // &#160;
    const ELLIPSIS            = "…";
    const LAQUO               = "«";
    const RAQUO               = "»";

    // @todo remove static
    public static $protected_tags = array('pre', 'code', 'script', 'style');
    protected $protected_tags_backups = array();

    public function fix($content)
    {

      $dom = $this->loadDOMDocument($content);

        $this->processDOM($dom, $dom);


        $content = preg_replace(array("/^\<\!DOCTYPE.*?<html><body>/si",
                                          "!</body></html>$!si"),
                                    "", $dom->saveHTML());
        $content = trim($content);


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
                    if (in_array($childNode->tagName, Fixer::$protected_tags)) {
                        continue;
                    }
                }

                $nodes[] = $childNode;
            }

            foreach ($nodes as $childNode) {
                if ($childNode instanceof \DOMText) {

                    $this->doFix($childNode, $node, $dom);

                }
                else {
                    $this->processDOM($childNode, $dom);
                }
            }
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

    private function doFix($childNode, $node, $dom)
    {
        $content = $childNode->wholeText;

        foreach (array('Ellipsis', 'FrenchQuotes') as $fixer_name) {
            $class = 'JoliTypo\\Fixer\\'.$fixer_name;
            $fixer = new $class();

            $content = $fixer->fix($content);
          }

        // @todo test is the node has changed?
       $node->replaceChild($dom->createTextNode($content), $childNode);
    }
}

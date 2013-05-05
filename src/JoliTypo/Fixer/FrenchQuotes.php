<?php

namespace JoliTypo\Fixer;

use JoliTypo\Fixer;

class FrenchQuotes
{
    public function fix($content)
    {

      $dom = new \DOMDocument('1.0', 'UTF-8');
      $dom->encoding = "UTF-8";
      $dom->strictErrorChecking = false;
      $dom->substituteEntities = false;
      $dom->formatOutput = false;



      $dom->loadHTML('<?xml encoding="UTF-8"><body>' . $content);

      foreach ($dom->childNodes as $item) {
        if ($item->nodeType === XML_PI_NODE) {
          $dom->removeChild($item); // remove hack
          break;
        }
      }



      $replaceRules = array('@"([^"]+)"@im' => Fixer::LAQUO.Fixer::NO_BREAK_THIN_SPACE."$1".Fixer::NO_BREAK_THIN_SPACE.Fixer::RAQUO);

      $this->process($dom, $replaceRules, $dom);

//      $file = tempnam('/tmp', 'lolz');
//      $dom->saveHTMLFile($file);
//
//      $content = file_get_contents($file);


      $content = preg_replace(array("/^\<\!DOCTYPE.*?<html><body>/si",
                                        "!</body></html>$!si"),
                                  "", $dom->saveHTML());

      //var_dump($content);


      $content = trim($content);

//$content = mb_convert_encoding($content, 'UTF-8', 'HTML-ENTITIES');


//        $content = preg_replace(
//            '@"([^"]+)"@im',
//            "&#171;".Fixer::NO_BREAK_THIN_SPACE."$1".Fixer::NO_BREAK_THIN_SPACE."&#187;",
//            $content
//        );

        return $content;
    }




  private  function process($node, $replaceRules, \DOMDocument $dom) {
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
                  $text = preg_replace(
                      array_keys($replaceRules),
                      array_values($replaceRules),
                      $childNode->wholeText);

                //$childNode->replaceWholeText($text);
//var_dump($text);
                  //$node->replaceChild(new \DOMText($text), $childNode);
                //var_dump($text);
                //$text = mb_convert_encoding($text, 'HTML-ENTITIES', 'UTF-8');
                //var_dump($text);

                //$docFrag = $dom->createDocumentFragment();
                //$docFrag->appendChild($dom->createTextNode($text));

                //var_dump($dom->createTextNode($text)->textContent);
                // TextNode is encoded
                 //$childNode->replaceData(0, mb_strlen($childNode->wholeText), $text);
                 //$node->replaceChild($docFrag, $childNode);
                 $node->replaceChild($dom->createTextNode($text), $childNode);
                 //$node->replaceChild($dom->createTextNode($childNode->wholeText), $childNode);
                 // $node->replaceChild($dom->createElement('text', $text), $childNode);
              }
              else {
                  $this->process($childNode, $replaceRules, $dom);
              }
          }
      }
  }
}

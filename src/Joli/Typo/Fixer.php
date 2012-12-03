<?php
namespace Joli\Typo;
use Symfony\Component\DomCrawler\Crawler;

class Fixer
{
    public function parse($html)
    {
        $crawler = new Crawler($html);

        $this->doParse($crawler);

        $body = $crawler->filterXPath('//body');

        // this sux
        $html = "";
        foreach ($body as $node) {
            $html .= $node->ownerDocument->saveHTML();
        }
        return $html;
    }

    private function doParse($crawler)
    {
        foreach ($crawler as $key => $domElement) {
            /** @var $domElement DOMElement */

            if ($domElement->nodeName === 'pre') {
                continue;
            }

            if ($domElement instanceof \DOMText) {
                var_dump($domElement->nodeValue);
                $domElement->nodeValue = "FAUX";
            }

            if ($domElement->hasChildNodes()) {
                $this->doParse($domElement->childNodes);
            }
        }
    }
}

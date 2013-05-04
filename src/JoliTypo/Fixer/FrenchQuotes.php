<?php

namespace JoliTypo\Fixer;

class FrenchQuotes
{
  public function fix($content)
  {
    $content = preg_replace('@"([^"]+)"@im', "&#171;&#8239;$1&#8239;&#187;", $content);

    return $content;
  }
}

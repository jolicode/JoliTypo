<?php

namespace JoliTypo\Fixer;

class Ellipsis
{
  public function fix($content)
  {
    $content = preg_replace('@\.{3,}@', "&#8230;", $content);

    return $content;
  }
}

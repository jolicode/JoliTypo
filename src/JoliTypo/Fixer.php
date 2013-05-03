<?php
namespace JoliTypo;

class Fixer
{

  const NO_BREAK_THIN_SPACE = "&#8239;";
  const NO_BREAK_SPACE = "&#160;";

  public function fix($content)
  {
    foreach (array('Ellipsis') as $fixer_name) {
      $class = 'JoliTypo\\Fixer\\'.$fixer_name;
      $fixer = new $class();

      $content = $fixer->fix($content);
    }

    return $content;
  }
}

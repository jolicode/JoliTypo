<?php
namespace JoliTypo;

class Fixer
{
  const NO_BREAK_THIN_SPACE = "&#8239;";
  const NO_BREAK_SPACE = "&#160;";

  protected $tags_to_backup = array('pre', 'code', 'script', 'style');

  public function fix($content)
  {
    $content = $this->backup($content);

    foreach (array('Ellipsis') as $fixer_name) {
      $class = 'JoliTypo\\Fixer\\'.$fixer_name;
      $fixer = new $class();

      $content = $fixer->fix($content);
    }

    $content = $this->unbackup($content);

    return $content;
  }

  private function backup($content)
  {
    // @todo
  }

  private function unbackup($content)
  {
    // @todo
  }
}

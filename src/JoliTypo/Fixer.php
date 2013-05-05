<?php
namespace JoliTypo;

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
    //$content = $this->backupProtected($content);

    foreach (array('Ellipsis', 'FrenchQuotes') as $fixer_name) {
      $class = 'JoliTypo\\Fixer\\'.$fixer_name;
      $fixer = new $class();

      $content = $fixer->fix($content);
    }

    //$content = $this->restoreProtected($content);

    return $content;
  }

  private function backupProtected($content)
  {
    $pattern = '@<pre([^</]+)</pre>@im';

    // PHP 5.3 compatibility...
    $backups = $this->protected_tags_backups;

    $content = preg_replace_callback(
        $pattern,
        function($match) use (&$backups) {
          $backup_name = "JOLITYPO_".(count($backups)+1);
          $backups[$backup_name] = $match[0];

          return $backup_name;
        },
        $content
    );

    $this->protected_tags_backups = $backups;

    return $content;
  }

  private function restoreProtected($content)
  {
    return str_replace(array_keys($this->protected_tags_backups), $this->protected_tags_backups, $content);
  }
}

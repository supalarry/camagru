<?php

require_once 'IView.php';

class View implements IView
{
  private $file;

  function __construct($file)
  {
    $this->file = $file;
  }

  function render(array $args = [])
  {
    ob_start();
    include($this->file);
    $var=ob_get_contents(); 
    ob_end_clean();
    return $var;
  }
}

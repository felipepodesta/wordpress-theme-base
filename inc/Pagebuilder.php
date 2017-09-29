<?php
namespace Vincit;

class Pagebuilder {
  public $data;
  public $templates;

  public function __construct($field_name = "pagebuilder") {
    $this->data = get_field($field_name);
    $this->templates = $this->loadTemplates();
  }

  public function loadTemplates() {
    $templates = [];

    foreach (glob(dirname(__FILE__) . "/templates/*") as $filename) {
      require_once($filename);

      $template = str_replace(".php", "", basename($filename));
      $function = "\\Vincit\\template\\$template";
      $templates[$template] = $function;

      if (!is_callable($function)) {
        throw new \Exception("$template isn't a function!");
      }
    }

    return $templates;
  }

  public function block($layout = null, $data = []) {
    if (is_null($layout)) {
      throw new \Exception("Selected block layout can't be null.");
    }

    ob_start();
    $this->templates[$layout]($data);
    $template = ob_get_clean();

    return $template;
  }

  public function getLayout() {
    $blocks = [];

    foreach ($this->data as $i => $block) {
      $blocks[] = $this->block($block["acf_fc_layout"], $block);
    }

    return join("\n", $blocks);
  }
}

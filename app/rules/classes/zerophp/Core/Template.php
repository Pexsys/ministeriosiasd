<?php
use ZeroPhp\Utils\StringUtils;

class Template
{
  protected $file;
  protected $values = array();

  public function __construct($file)
  {
    if (!file_exists($file)) throw new \Exception(sprintf(ERROR_LOADING_TEMPLATE_FILE, $file));
    $this->file = $file;
  }

  public function set($key, $value)
  {
    $this->values[$key] = $value;
  }

  public function get($key)
  {
    return $this->values[$key];
  }

  public function setValues($values)
  {
    $this->values = $values;
  }

  public function getValues()
  {
    return $this->values;
  }

  public function output()
  {
    return $this->outputFromView($this->file);
  }

  private function outputFromView($view_filename)
  {
    $view = file_get_contents($view_filename);
    $new_output_array = array();

    $this->process($view, $new_output_array);

    $new_output = implode("", $new_output_array);

    return $new_output;
  }

  private function process(&$view, &$new_output_array)
  {
    //substitui variáveis
    foreach ($this->values as $key => $value) {
      $tag_to_replace = "{@$key}";
      $view = str_replace($tag_to_replace, $value, $view);
    }

    //XSRF/CSRF - Substitui marcação
    $view = str_replace("{@XSRF}", sprintf("<input type='hidden' name='securitytoken' value='%s'>", Session::Get('SecurityToken')), $view);
    $view = str_replace("{@CSRF}", sprintf("<input type='hidden' name='securitytoken' value='%s'>", Session::Get('SecurityToken')), $view);
    $view = str_replace("{@xsrf}", sprintf("<input type='hidden' name='securitytoken' value='%s'>", Session::Get('SecurityToken')), $view);
    $view = str_replace("{@csrf}", sprintf("<input type='hidden' name='securitytoken' value='%s'>", Session::Get('SecurityToken')), $view);
    $view = str_replace("{@microtime}", microtime(), $view);
    $view = str_replace("{@SERVER_NAME}", $_SERVER['SERVER_NAME'], $view);
    $view = str_replace("{@ENVIRONMENT}", CFG::Get()->Var('Env'), $view);
    $view = str_replace("{@ROOT}", CFG::Root(), $view);    

    //processa cada linha
    $lines = explode("\n", $view);
    foreach ($lines as $line) {
      //se tiver código PHP para rodar
      if (StringUtils::StrStartWith(trim($line), "{@php")) {
        $php_code = trim($line);
        StringUtils::StrRemoveLastChar($php_code);
        $php_code = str_replace("{@php", "", $php_code);

        ob_start(); //abre um buffer para armazenar o output da execução do código em uma variável
        eval($php_code); //executa o código php
        $output_eval = ob_get_contents(); //joga o output em uma variável
        ob_end_clean(); //libera o buffer

        array_push($new_output_array, $output_eval);
      } else if (StringUtils::StrStartWith(trim($line), "{@include")) {
        $view_to_include = trim($line);
        StringUtils::StrRemoveLastChar($view_to_include);
        $view_to_include = str_replace("{@include ", "", $view_to_include);
        $view_to_include = str_replace("\"", "", $view_to_include);

        array_push($new_output_array, $this->outputFromView($view_to_include));
      } else {
        array_push($new_output_array, $line);
      }
    }
  }
}

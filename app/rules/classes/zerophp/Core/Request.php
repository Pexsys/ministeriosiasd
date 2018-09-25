<?php
use ZeroPhp\Utils\TokenHelper;

class Request
{
  public $id;

  public $route;
  public $method;
  public $headers;
  public $params;
  public $contents;
  public $authorization;

  public function __construct($route)
  {
    $this->route = $route;
    $this->method = $_SERVER['REQUEST_METHOD'];
    $this->params = $_SERVER['REQUEST_METHOD'] == "POST" ? $this->getPostParams() : $this->getQueryString();
    $this->headers = apache_request_headers();
    $this->contents = $this->getContents();
    $this->id = TokenHelper::GenerateToken(16) . time();
  }

  private function getQueryString()
  {
    $query_str = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
    $array_params = null;
    if ($query_str != null) parse_str($query_str, $array_params);
    $array_params = $this->security($array_params);
    return $array_params;
  }

  private function getPostParams()
  {
    $get_params = $this->getQueryString();
    $post_params = $this->security($_POST);
    $array_combine = array_merge($get_params, $post_params);
    return $array_combine;
  }

  private function getContents()
  {
    $obj = json_decode(file_get_contents('php://input'));
    return $this->security((array) $obj);
  }

  private function security($array)
  {
    if ($array == null) return array();
    foreach ($array as &$valor) {
      if (gettype($valor) == "object") {
        $valor = $this->security((array) $valor);
      } elseif (gettype($valor) == "array") {
        $valor = $this->security($valor);
      } else {
        $valor = htmlspecialchars($valor, ENT_QUOTES);
      }
    }
    return $array;
  }
}

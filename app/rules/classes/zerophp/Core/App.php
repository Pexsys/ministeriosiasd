<?php
use ZeroPhp\Interfaces\AppInterface;
use ZeroPhp\Utils\StringUtils;
use ZeroPhp\Utils\TokenHelper;

class App implements AppInterface
{
  private $route;
  private $registered_routes;

  public function __construct()
  {
    // $this->checktoken();
    Session::Set('SecurityToken', TokenHelper::GenerateToken(64));

    $this->route = "/";
    $this->registered_routes = array('GET' => array(), 'POST' => array(), 'PUT' => array(), 'DELETE' => array());
  }

  public function get($route, $function, $attributes = null): void
  {
    if (array_key_exists($route, $this->registered_routes["GET"])) throw new Exception(THE_ROUTE_ALREADY_EXISTS);

    while (StringUtils::StrEndWith(trim($route), "/")) StringUtils::StrRemoveLastChar($route);
  
    $this->registered_routes["GET"][$route] = array('function' => $function, 'attributes' => $attributes);
  }

  public function post($route, $function, $attributes = null): void
  {
    if (array_key_exists($route, $this->registered_routes["POST"])) throw new Exception(THE_ROUTE_ALREADY_EXISTS);

    while (StringUtils::StrEndWith(trim($route), "/")) StringUtils::StrRemoveLastChar($route);

    $this->registered_routes["POST"][$route] = array('function' => $function, 'attributes' => $attributes);
  }

  public function put($route, $function, $attributes = null): void
  {
    if (array_key_exists($route, $this->registered_routes["PUT"])) throw new Exception(THE_ROUTE_ALREADY_EXISTS);

    while (StringUtils::StrEndWith(trim($route), "/")) StringUtils::StrRemoveLastChar($route);

    $this->registered_routes["PUT"][$route] = array('function' => $function, 'attributes' => $attributes);
  }

  public function delete($route, $function, $attributes = null): void
  {
    if (array_key_exists($route, $this->registered_routes["DELETE"])) throw new Exception(THE_ROUTE_ALREADY_EXISTS);

    while (StringUtils::StrEndWith(trim($route), "/")) StringUtils::StrRemoveLastChar($route);

    $this->registered_routes["DELETE"][$route] = array('function' => $function, 'attributes' => $attributes);
  }

  public function run()
  {
    $this->route = isset($_REQUEST["url"]) ? "/" . trim($_REQUEST["url"])  : "/";

    while (StringUtils::StrEndWith(trim($this->route), "/")) StringUtils::StrRemoveLastChar($this->route);

    $request = new Request($this->route);
    $response = new Response();
    $METHOD = $request->method;

    if ($METHOD == "GET" || $METHOD == "POST" || $METHOD == "PUT" || $METHOD == "DELETE") {
      if (array_key_exists($this->route, $this->registered_routes[$METHOD])) {
        $this->execute_attributes($request, $this->registered_routes[$METHOD][$this->route]["attributes"]);

        $function = $this->registered_routes[$METHOD][$this->route]["function"];
        $result_response = $function($request, $response);

        $this->run_output($result_response);
      } else {
        http_response_code(404);
      }
    } else {
      http_response_code(404);
    }
  }

  private function run_output($result_response)
  {
    http_response_code($result_response->statusCode);
    header("Content-Type:$result_response->contentType; charset=$result_response->charset");
    header_remove('X-Powered-By');
    header("Access-Control-Allow-Methods: PUT");
    header("Access-Control-Allow-Methods: DELETE");

    foreach ($result_response->headers as $valor) header($valor);

    if ($result_response->content !== null) {
      if (trim(strtolower($result_response->contentType)) == "application/json") {
        echo json_encode($result_response->content);
      } else {
        echo $result_response->content;
      }
    }
  }

  private function execute_attributes($request, $attributes)
  {
    if ($attributes !== null && count($attributes) > 0) {
      foreach ($attributes as $item) {
        $class_type = $item[0];
        $class_attributes = count($item) > 1 ? $item[1] : null;

        $class = new $class_type();

        if (property_exists($class, "attributes") == false) throw new Exception(THE_ATTRIBUTE_CLASS_MUST_HAVE_THE_FIELD_attributes);
        if (method_exists($class, "run") == false) throw new Exception(THE_ATTRIBUTE_CLASS_MUST_HAVE_THE_METHOPD_run);

        $class->attributes = $class_attributes;

        $result = $class->run(new Context($request));

        if ($result !== null) {
          if ($result instanceof Response === false) {
            throw new Exception(THE_RETURN_OF_AN_ATTRIBUTE_CLASS_MUST_BE_OF_TYPE_RESPONSE);
          } else {
            $this->run_output($result);
            die();
          }
        }
      }
    }
  }

  private function checktoken()
  {
    if (Session::Get('SecurityToken') == null) return;

    if (count($_POST) > 0) {
      //check
      if (isset($_POST['securitytoken'])) {
        if ($_POST['securitytoken'] != Session::Get('SecurityToken')) {
          http_response_code(400);
          die();
        }
      } else {
        http_response_code(400);
        die();
      }
    } else {
      try {
        if ($_SERVER['REQUEST_METHOD'] == "PUT" || $_SERVER['REQUEST_METHOD'] == "DELETE") {
          $contents = file_get_contents('php://input');
          $obj = json_decode($contents);

          $array = (array) $obj;

          //check
          if (isset($array['securitytoken'])) {
            if ($array['securitytoken'] != Session::Get('SecurityToken')) {
              http_response_code(400);
              die();
            }
          } else {
            http_response_code(400);
            die();
          }
        }
      } catch (\Exception $exception) {
        http_response_code(400);
        die();
      }
    }
  }
}

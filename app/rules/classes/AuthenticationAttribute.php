<?php
Session::Start();
class AuthenticationAttribute
{
  public $attributes;

  public function run($context)
  {
    $response = new Response();
    $response->setContentType("application/json");
    $response->addHeader("Pragma: no-cache");

    //API authentication
    if (in_array("header", $this->attributes)) {
      if (in_array("Authorization", $this->attributes)) {

        if (!isset($context->request->headers["Authorization"])) {
          $response->setStatusCode(401);
          return $response;
        }

        preg_match('/Bearer\s(\S+)/', $context->request->headers["Authorization"], $matches);
        if (!isset($matches[1])) {
          $response->setStatusCode(401);
          return $response;
        }

        $rs = CONN::Get()->execute("SELECT * FROM API_AUTH WHERE ROUTE = ? AND TOKEN = ?", array($context->request->route, $matches[1]));
        if ($rs->EOF) {
          $response->setStatusCode(404);
          return $response;
        }

        Session::User("id", $rs->fields['PERSON_ID']);
        $context->request->authorization = $rs->fields;
      }

      //Autenticado
    } elseif (in_array("authenticated", $this->attributes)) {
      if (!Session::KeyExists('USER')) {
        $response->setStatusCode(401);
        return $response;
      }
    }
  }
}

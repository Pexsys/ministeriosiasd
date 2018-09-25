<?php
class Context
{
  public $request;

  public function __construct($request)
  {
    $this->request = $request;
  }

  public function redirect($url): void
  {
    $redirectUrl = $this->GetBasePath() . $url;
    header("Location: $redirectUrl");
    exit;
  }

  public function GetBasePath(): string
  {
    return str_replace("index.php", "", $_SERVER["SCRIPT_NAME"]);
  }
}

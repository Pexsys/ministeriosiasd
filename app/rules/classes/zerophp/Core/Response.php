<?php
class Response
{
  public $content;
  public $statusCode;
  public $contentType; //text/html | text/plain | application/json
  public $charset;
  public $headers;

  public function __construct()
  {
    $this->content = null;
    $this->statusCode = 200;
    $this->contentType = "text/html";
    $this->charset = "utf-8";
    $this->headers = array();
  }

  public function setContent($content): void
  {
    $this->content = $content;
  }

  public function setStatusCode($statusCode): void
  {
    $this->statusCode = $statusCode;
  }

  public function setContentType($contentType): void
  {
    $this->contentType = $contentType;
  }

  public function setCharset($charset): void
  {
    $this->charset = $charset;
  }

  public function addHeader($header): void
  {
    array_push($this->headers, $header);
  }

  public function redirect($url): void
  {
    $redirectUrl = $this->GetBasePath() . $url;
    header("Location: $redirectUrl");
  }

  public function GetBasePath(): string
  {
    return str_replace("index.php", "", $_SERVER["SCRIPT_NAME"]);
  }
}

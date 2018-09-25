<?php
class AppFactory
{
  public static function create(): App
  {
    $app = new App();
    foreach (glob(__DIR__ . "/../../../../api/routes/*.php") as $filename) require $filename;
    $app->run();
    return $app;
  }
}

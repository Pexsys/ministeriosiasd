<?php
class LogHelper
{
  public static function LogPath()
  {
    $logPath = "logs/log-" . Formatter::DateTimeNow('d-m-Y') . ".txt";

    return $logPath;
  }

  public static function Save($message)
  {
    try {
      $dateFormate = Formatter::DateTimeNow();

      // file_put_contents(self::LogPath(),  "[$dateFormate] - $message" . PHP_EOL . PHP_EOL, FILE_APPEND | LOCK_EX);
      error_log("[$dateFormate] - $message" . PHP_EOL . PHP_EOL);
    } catch (Exception $e) {
    }
  }

  public static function Request($context)
  {
    try {
      $user = Session::KeyExists('USER') ? "(" . Session::User('id') . ") " . Session::User('nome') : "UNAUTHENTICATED";

      $dateFormate = Formatter::DateTimeNow();
      $request_id = $context->request->id;
      $contents = json_encode($context->request->contents);
      $params = json_encode($context->request->params);

      $message = "[$dateFormate] [$user] - [$request_id] [{$context->request->method}] - ROUTE=> {$context->request->route} - CONTENTS=> {$contents} - PARAMS=> {$params}" . PHP_EOL . PHP_EOL;
      $message = str_replace("\\", "", $message);

      // file_put_contents(self::LogPath(), $message);
      error_log($message);
    } catch (Exception $exception) {
    }
  }

  public static function Object($request, $message, $object)
  {
    try {
      $user = Session::KeyExists('USER') ? "({Session::User('id')}) {Session::User('nome')}" : "UNAUTHENTICATED";

      $dateFormate = Formatter::DateTimeNow();
      $request = json_encode($request);
      $object = json_encode($object);

      $message = "[$dateFormate] [$user] [Log] - REQUEST=> [$request] - MESSAGE=> {$message} - OBJECT=> {$object}" . PHP_EOL . PHP_EOL;
      $message = str_replace("\\", "", $message);

      // file_put_contents(self::LogPath(), $message);
      error_log($message);
    } catch (Exception $exception) {
    }
  }
}

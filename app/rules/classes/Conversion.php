<?php
class Conversion
{
  public static function DataTimeToMysql($input, $fromSignal = "/", $spaceTime = " ")
  {
    try {
      if (is_null($input)) return NULL;
      if (!isset($input) || strlen(trim($input)) == 0) return "";
      $parts = explode(" ", trim($input));
      $horario = count($parts) > 1 ? $parts[1] : null;
      $toSignal = ($fromSignal == "/" ? "-" : "/");
      $data_parts = explode($fromSignal, $parts[0]);
      if ($data_parts[1] == 0) return "";
      if (count($data_parts) == 3) {
        return $data_parts[2] . $toSignal . $data_parts[1] . $toSignal . $data_parts[0] . (isset($horario) ? $spaceTime . $horario : "");
      } else {
        return "UNKNOW DATA";
      }
    } catch (Exception $e) {
    }
  }

  public static function DataMysqlToMktime($input)
  {
    try {
      if (!isset($input) || strlen(trim($input)) == 0) return "";
      $data_parts =  explode("-", trim($input));
      if ($data_parts[1] == 0) return "";
      if (count($data_parts) == 3) {
        return mktime(0, 0, 0, $data_parts[1], $data_parts[2], $data_parts[0]);
      } else {
        return "UNKNOW DATA";
      }
    } catch (Exception $e) {
    }
  }

  public static function StrToDate($pValue, $pic = "Y-m-d H:i")
  {
    if (!isset($pValue) || is_null($pValue) || empty($pValue)) return null;
    $pValue = str_replace("/", "-", $pValue);
    return date($pic, strtotime($pValue));
  }
}

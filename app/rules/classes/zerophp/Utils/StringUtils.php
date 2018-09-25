<?php

namespace ZeroPhp\Utils;

class StringUtils
{
  public static function StrStartWith($str, $search)
  {
    return substr($str, 0, strlen($search)) == $search;
  }

  public static function StrEndWith($str, $search)
  {
    return substr($str, -strlen($search), 1) == $search;
  }

  public static function StrRemoveLastChar(&$str)
  {
    $str = trim(substr($str, 0, strlen($str) - 1));
  }

  public static function StrContains($str, $char)
  {
    if (strpos($str, $char) !== false) {
      return true;
    }
    return false;
  }

  public static function StrGetChar($str, $index)
  {
    return substr($str, $index, 1);
  }
}

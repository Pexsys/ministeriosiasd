<?php

namespace ZeroPhp\Utils;

class TokenHelper
{
  public static function GenerateToken($lenght)
  {
    $chars = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "V", "Z");
    $password = "";
    for ($i = 0; $i < $lenght; $i++) $password .= $chars[rand(0, count($chars) - 1)];
    return $password;
  }
}

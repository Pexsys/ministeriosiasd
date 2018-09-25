<?php
class Cookie
{
  public static function GetDomainAddress()
  {
    return str_replace(":8080", "", $_SERVER["HTTP_HOST"]);
  }

  public static function Set(string $name, string $value = "", int $expire = null, string $path = "/", string $domain = "", bool $secure = true, bool $httponly = false)
  {
    if ($expire == null) $expire = mktime(0, 0, 0, date("m") + 1, date("d"), date("Y"));
    setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
  }

  public static function Get(string $name)
  {
    return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
  }

  public static function Delete(string $name)
  {
    Cookie::Set($name, "", time() - 3600);
  }
}

<?php
class CONFIG_FACTORY
{

  protected static $variables;

  public function __construct()
  {
    if (!isset(self::$variables['Root'])) {
      $config = parse_ini_file(__DIR__ . "/../../../_config/_{$_SERVER["SERVER_NAME"]}.ini");
      self::$variables = $config;
      $version = parse_ini_file(__DIR__ . "/../../../app/_version.ini");
      self::$variables['AppVersion'] = $version['AppVersion'];
    }
  }

  public static function Instance()
  {
    return new self();
  }

  public static function Var($var)
  {
    if (!isset(self::$variables[$var])) return;
    return self::$variables[$var];
  }
}

class CFG extends CONFIG_FACTORY
{

  static function Get()
  {
    return CONFIG_FACTORY::Instance();
  }

  public static function Root()
  {
    return self::Get()->Var('Root');
  }

  public static function AppVersion()
  {
    return self::Get()->Var('AppVersion');
  }

  private static function rule($node, $default)
  {
    //2022-08-18 00:00:00
    //2022-08-18 00:00:00|2022-08-19 00:00:00
    if (Formatter::Empty(self::Get()->Var($node))) return $default;
    $dh = Formatter::DateTimeNow();
    $p = explode('|', self::Get()->Var($node));
    $p1 = ($dh > $p[0]);
    $p2 = (empty($p[1]) ? true : ($dh < $p[1]));
    return ($p1 && $p2);
  }

  public static function Blocked()
  {
    return (static::rule('Bloqueado', false) || !static::rule('Permitido', true));
  }
}

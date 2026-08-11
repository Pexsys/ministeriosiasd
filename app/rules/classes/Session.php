<?php
class Session
{
  public static function Start()
  {
    session_start(
      [
        'cache_limiter' => 'nocache',
        'cookie_httponly' => true,
        'cookie_lifetime' => 0,
        'cookie_secure' => !HtmlHelper::IS_LOCALHOST(),
        'name' => 'sge-mda-aps',
        'use_only_cookies' => true,
        'use_strict_mode' => true,
      ]
    );
  }

  public static function Id()
  {
    return session_id();
  }

  public static function KeyExists($s, $k = null)
  {
    if (!isset($_SESSION) || is_null($_SESSION) || empty($_SESSION)) return false;
    if (!isset($_SESSION[$s]) || is_null($_SESSION[$s]) || empty($_SESSION[$s])) return false;
    return !is_null($k) ? isset($_SESSION[$s][$k]) : isset($_SESSION[$s]);
  }

  public static function Get($s, $k = null)
  {
    return (!isset($_SESSION[$s]))
      ? null
      : (!is_null($k) && static::KeyExists($s, $k) ? $_SESSION[$s][$k] : $_SESSION[$s]);
  }

  public static function Set($s, $k, $v = null)
  {
    if (is_null($v)):
      $_SESSION[$s] = $k;
    else:
      $_SESSION[$s][$k] = $v;
    endif;
  }

  public static function SetNull($s, $k)
  {
    $_SESSION[$s][$k] = null;
  }

  public static function Remove($s, $k = null)
  {
    if (!is_null($k)):
      unset($_SESSION[$s][$k]);
    else:
      unset($_SESSION[$s]);
    endif;
  }

  public static function Clear()
  {
    static::Remove('MODAL');
    static::Remove('SAVED_USER');
    static::Remove('SURVEY');
    static::Remove('USER');
    static::Remove('EAI');
    if (session_status() === PHP_SESSION_ACTIVE) session_destroy();
  }

  public static function LoginVerify()
  {
    $temPerfil = isset($_SESSION['PESSOA']['ssid']);
    if (!$temPerfil):
      session_destroy();
      header("Location: " . CFG::Root() . "index.php");
      exit;
    endif;
  }
}

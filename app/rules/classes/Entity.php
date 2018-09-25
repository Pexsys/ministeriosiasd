<?php
class CONNECTION_FACTORY
{

  protected static $connection;

  public function __construct()
  {
    if (!isset(self::$connection)) {
      $config = parse_ini_file(__DIR__ . "/../../../_dbconnect/_{$_SERVER["SERVER_NAME"]}.ini");
      self::$connection = newADOConnection($config['Type']);
      self::$connection->setConnectionParameter(MYSQLI_SET_CHARSET_NAME, 'utf8mb4');
      self::$connection->setCharSet('utf');
      self::$connection->connect($config['Host'], $config['User'], $config['Password'], $config['Database']);
      self::$connection->setFetchMode(ADODB_FETCH_ASSOC);
    }
    if (self::$connection === false) {
      exit("Erro ao connectar ao banco de dados");
    }
  }

  public static function Instance()
  {
    return new self();
  }

  public function Get()
  {
    return self::$connection;
  }
}

class CONN
{
  public static function Get()
  {
    return CONNECTION_FACTORY::Instance()->Get();
  }

  public static function Domain($table)
  {
    $arr = array();
    $result = static::get()->Execute("SELECT id, ds, cd FROM $table ORDER BY ds");
    foreach ($result as $k => $fields):
      $arr[] = array(
        "value"  => $fields["id"],
        "label"  => utf8_encode($fields["ds"]),
        "sub" => $fields["cd"]
      );
    endforeach;
    return $arr;
  }
}

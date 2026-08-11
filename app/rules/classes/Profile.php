<?php
class Profile
{
  public static function Get($cd = NULL)
  {
    $arr = array();
    $query = "SELECT DISTINCT td.id, td.cd, td.icon, td.ds, td.url
			  FROM CD_PERSON_PROFILE cpp
		INNER JOIN TB_PROFILE_ITEM tpi ON (tpi.id_tb_profile = cpp.id_tb_profile)
		INNER JOIN TB_DASHBOARD td ON (td.id = tpi.id_tb_dashboard)
			 WHERE cpp.id_cd_person = ?";
    if (isset($cd) && !empty($cd)):
      $query .= " AND td.cd LIKE '$cd.%'";
    else:
      $query .= " AND LENGTH(td.cd) = 2";
    endif;
    $query .= " ORDER BY td.cd";
    $result = CONN::get()->Execute($query, array($_SESSION['PESSOA']['id']));
    foreach ($result as $key => $fields):
      $arr[$fields['id']] = array(
        "opt"   => $fields['ds'],
        "ico"   => $fields['icon'],
        "url"   => $fields['url'],
        "active" => false,
        "child"  => Profile::Get($fields['cd'])
      );
    endforeach;
    return $arr;
  }

  public static function SaveSession($result)
  {
    $_SESSION['PESSOA']['ssid'] = session_id();
    $_SESSION['PESSOA']['email'] = $result->fields['email'];
    $_SESSION['PESSOA']['id'] = $result->fields['id'];
  }

  public static function SetSessionLogin($result)
  {
    Session::Start();
    session_regenerate_id(true);
    static::SaveSession($result);
  }

  public static function VerificaPerfil($id_cd_person)
  {
    //VERIFICA SE TEM AO MENOS UM PERFIL, SE NAO INSERE PERFIL BASICO 0-GUEST.
    $resperf = CONN::get()->Execute("SELECT * FROM CD_PERSON_PROFILE WHERE id_cd_person = ?", array($id_cd_person));
    if ($resperf->EOF):
      CONN::get()->Execute("
      INSERT INTO CD_PERSON_PROFILE(id_cd_person, id_tb_profile)
      VALUES (?,?)
      ", array($id_cd_person, 0));
    endif;
  }
}

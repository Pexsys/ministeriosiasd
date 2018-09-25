<?php
class Profile
{

  public static function HasAccess($result)
  {
    $dh = Formatter::DateTimeNow();
    $res = CONN::Get()->execute("
        SELECT DISTINCT 1
          FROM EVENT_PERSON_PROFILE epp
    INNER JOIN PROFILE_MENU pm ON (pm.PROFILE = epp.PROFILE AND (pm.EVENT IS NULL OR pm.EVENT = epp.EVENT) AND (pm.DH_INI_VALID IS NULL OR pm.DH_INI_VALID <= ?) AND (pm.DH_FIM_VALID IS NULL OR pm.DH_FIM_VALID >= ?))
    INNER JOIN MENU d ON (d.ID = pm.MENU)
     LEFT JOIN FUNCTIONALITY f ON (f.ID = d.FUNCTIONALITY)
          WHERE epp.PERSON = ?
            AND epp.EVENT = ?
    UNION
        SELECT DISTINCT 1
          FROM PROFILE p
    INNER JOIN PROFILE_MENU pm ON (pm.PROFILE = p.ID AND (pm.DH_INI_VALID IS NULL OR pm.DH_INI_VALID <= ?) AND (pm.DH_FIM_VALID IS NULL OR pm.DH_FIM_VALID >= ?))
    INNER JOIN MENU d ON (d.ID = pm.MENU)
      LEFT JOIN FUNCTIONALITY f ON (f.ID = d.FUNCTIONALITY)
          WHERE p.DS = ?
    ", array($dh, $dh, $result->fields['ID'], $result->fields['EVENT'], $dh, $dh, $result->fields['POSITION']));
    return !$res->EOF;
  }

  public static function Get()
  {
    $dh = Formatter::DateTimeNow();
    $result = array();

    if (Session::KeyExists('USER', 'event')) $result = CONN::Get()->getAll("
          SELECT DISTINCT
                 d.ID, d.CD,
                 IF(f.ROUTE IS NULL, NULL, em.SPLIT) AS SPLIT,
                 IF(f.ROUTE IS NULL, d.DS, IF(em.MODAL IS NULL, d.DS, em.MODAL)) AS DS,
                 IF(f.ROUTE IS NULL, d.ICON, IF(em.MODAL IS NULL, d.ICON, CONCAT('fas ', em.ICON))) AS ICON,
                 IF(f.ROUTE IS NULL, NULL, IF(epp.MODAL IS NULL, NULL, epp.MODAL)) AS MODAL,
                 f.ROUTE
        FROM EVENT_PERSON_PROFILE epp
      INNER JOIN PROFILE p ON (p.ID = epp.PROFILE)
      INNER JOIN PROFILE_MENU pm ON (pm.PROFILE = p.ID AND (pm.EVENT IS NULL OR pm.EVENT = epp.EVENT) AND (pm.DH_INI_VALID IS NULL OR pm.DH_INI_VALID <= ?) AND (pm.DH_FIM_VALID IS NULL OR pm.DH_FIM_VALID >= ?))
      INNER JOIN MENU d ON (d.ID = pm.MENU)
       LEFT JOIN EVENT_MODAL em ON (em.ID = epp.MODAL)
       LEFT JOIN FUNCTIONALITY f ON (f.ID = d.FUNCTIONALITY)
           WHERE epp.PERSON = ?
             AND epp.EVENT = ?
        ORDER BY d.CD, DS, SPLIT
    ", array($dh, $dh, Session::User('id'), Session::User('event')));

    if (Session::KeyExists('USER', 'event') && count($result) == 0) $result = CONN::Get()->getAll("
          SELECT DISTINCT 
                 d.ID, d.CD,
                 NULL as SPLIT,
                 d.DS,
                 d.ICON,
                 NULL AS MODAL,
                 f.ROUTE
            FROM PROFILE p
      INNER JOIN PROFILE_MENU pm ON (pm.PROFILE = p.ID AND (pm.DH_INI_VALID IS NULL OR pm.DH_INI_VALID <= ?) AND (pm.DH_FIM_VALID IS NULL OR pm.DH_FIM_VALID >= ?))
      INNER JOIN MENU d ON (d.ID = pm.MENU)
       LEFT JOIN FUNCTIONALITY f ON (f.ID = d.FUNCTIONALITY)
           WHERE (pm.EVENT IS NULL OR pm.EVENT = ?) 
             AND p.DS = ?
        ORDER BY d.CD
    ", array($dh, $dh, Session::User('event'), Session::User('position')));

    if (count($result) == 0) $result = CONN::Get()->getAll("
          SELECT DISTINCT
                 d.ID, d.CD,
                 NULL as SPLIT,
                 d.DS,
                 d.ICON,
                 NULL AS MODAL,
                 f.ROUTE
            FROM PROFILE p
      INNER JOIN PROFILE_MENU pm ON (pm.PROFILE = p.ID AND (pm.DH_INI_VALID IS NULL OR pm.DH_INI_VALID <= ?) AND (pm.DH_FIM_VALID IS NULL OR pm.DH_FIM_VALID >= ?))
      INNER JOIN MENU d ON (d.ID = pm.MENU)
       LEFT JOIN FUNCTIONALITY f ON (f.ID = d.FUNCTIONALITY)
           WHERE p.DS = ?
        ORDER BY d.d.CD
    ", array($dh, $dh, Session::User('position')));

    return Menu::MakeOptions($result);
  }

  public static function SaveSession($result)
  {
    Session::User('id', $result['ID']);
    Session::User('area', $result['AREA']);
    Session::User('club_id', $result['CLUB_ID']);
    Session::User('is_real', $result['IS_REAL']);
    Session::User('sup_id', $result['SUP_ID']);
    Session::User('club', $result['CLUB_NAME']);
    Session::User('event', $result['EVENT']);
    Session::User('mail', $result['EMAIL']);
    Session::User('name', $result['NAME']);
    Session::User('position', $result['POSITION']);
    Session::User('region_name', $result['REGION_NAME']);
    Session::User('region', $result['REGION_ID']);
    Session::User('sgc', $result['SGC']);
  }

  public static function SetSessionLogin($result)
  {
    Session::Start();
    session_regenerate_id(true);
    Session::User('ssid', Session::Id());
    static::SaveSession($result);
  }

  public static function VerificaPerfil()
  {
    $temPerfil = Session::KeyExists('USER', 'ssid');
    if (!$temPerfil) :
      Session::Clear();
      header("Location: " . CFG::Root());
      exit;
    endif;
    return $temPerfil;
  }

  public static function InsertByPessoaID($EVENT, $EVENT_PERSON, $PROFILE)
  {
    $rs = CONN::Get()->execute("SELECT * FROM EVENT_PERSON WHERE EVENT = ? AND PERSON = ?", array($EVENT, $EVENT_PERSON));
    if (!$rs->EOF) static::Insert($EVENT, $EVENT_PERSON, $PROFILE);
  }

  public static function Insert($EVENT, $EVENT_PERSON, $PROFILE)
  {
    $rs = CONN::Get()->execute("
			SELECT 1 FROM EVENT_PERSON_PROFILE
			WHERE EVENT = ?
        AND PERSON = ?
			  AND PROFILE = ?
		", array($EVENT, $EVENT_PERSON, $PROFILE));
    if ($rs->EOF) :
      CONN::Get()->execute("INSERT INTO EVENT_PERSON_PROFILE (EVENT, PERSON, PROFILE)VALUES (?,?,?)", array($EVENT, $EVENT_PERSON, $PROFILE));
    endif;
  }

  public static function Apply($EVENT, $EVENT_PERSON, $POSITION)
  {
    $rules = static::Rules($POSITION);
    foreach ($rules as $k => $p) static::InsertByPessoaID($EVENT, $EVENT_PERSON, $p);
  }

  public static function Rules($POSITION)
  {
    $arr = array();
    $rs = CONN::Get()->execute("SELECT ID FROM PROFILE WHERE DS = ? ", array($POSITION));
    if (!$rs->EOF) foreach ($rs as $k => $fields) $arr[] = $fields['ID'];
    return $arr;
  }

  public static function RemoveItem($PROFILE, $EVENT = null)
  {
    $onJoinEvent = " IS NULL";
    $binding = array($PROFILE);
    if (isset($EVENT)):
      $onJoinEvent = " = ?";
      $binding[] = $EVENT;
    endif;
    CONN::Get()->execute("DELETE FROM PROFILE_MENU WHERE PROFILE = ? AND EVENT$onJoinEvent",  $binding);
  }

  public static function RemoveProfilUserItemId($id)
  {
    CONN::Get()->execute("DELETE FROM EVENT_PERSON_PROFILE WHERE ID = ?", array($id));
  }

  public static function InsertProfilelUserItem($event, $person, $profile, $modal = null)
  {
    CONN::Get()->execute("INSERT INTO EVENT_PERSON_PROFILE (EVENT, PERSON, PROFILE, MODAL) VALUES (?,?,?,?)", array($event, $person, $profile, $modal));
  }

  public static function RemoveItemId($id)
  {
    CONN::Get()->execute("DELETE FROM PROFILE_MENU WHERE ID = ?", array($id));
  }

  public static function InsertItem($profile, $menu, $event = null)
  {
    CONN::Get()->execute("INSERT INTO PROFILE_MENU (EVENT, PROFILE, MENU) VALUES (?,?,?)", array($event, $profile, $menu));
  }

  public static function UpdateItemId($id, $fields)
  {
    $fielder = array(
      "dhi" => "DH_INI_VALID",
      "dhf" => "DH_FIM_VALID",
    );
    $bind = array();
    $strSet = array();
    foreach ($fields as $key => $value):
      $strSet[] = $fielder[$value['fl']] . " = ?";
      if (@$value['fl'] == "dhi" || @$value['fl'] == "dhf") $bind[] = Conversion::DataTimeToMysql(Formatter::EmptyOr(@$value['vl'], NULL));
    endforeach;
    CONN::Get()->execute("UPDATE PROFILE_MENU SET " . implode(", ", $strSet) . " WHERE ID = ?", array(...$bind, $id));
  }
}

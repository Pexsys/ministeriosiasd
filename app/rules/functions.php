<?php
@require_once(__DIR__ . '/../../vendor/autoload.php');

function responseMethod()
{
  if (CFG::Blocked()) {
    echo json_encode(array("blocked" => true));
    exit;
  }

  error_reporting(E_ALL & ~E_NOTICE); //& ~ E_DEPRECATED
  ini_set('display_errors', TRUE);
  ini_set('display_startup_errors', TRUE);
  header('Content-type: application/json');
  // Getting the json data from the request
  $response = '';

  $json_data = json_decode(json_encode(empty($_POST) ? $_GET : $_POST));
  // Checking if the data is null..
  if (is_null($json_data)) :
    $response = json_encode(array("status" => -1, "message" => "Insufficient paramaters!"));
  elseif (empty($json_data->{'MethodName'})) :
    $response = json_encode(array("status" => 0, "message" => "Invalid function name!"));
  else :
    $MethodName = $json_data->MethodName;
    if (isset($json_data->{'data'})) :
      $response = $MethodName(objectToArray($json_data->{'data'}));
    else :
      $response = $MethodName(objectToArray($json_data));
    endif;
  endif;

  echo json_encode($response);
}

function dd($obj)
{
  var_dump($obj);
  exit;
}

function url_exists($url)
{
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_NOBODY, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
  curl_exec($ch);
  $headers = curl_getinfo($ch);
  curl_close($ch);
  return (@$headers['http_code'] === 200);
}

function get_include_contents($filename)
{
  if (is_file($filename)) {
    ob_start();
    include $filename;
    return ob_get_clean();
  }
  return '';
}

function objectToArray($d)
{
  if (is_object($d)) {
    // Gets the properties of the given object
    // with get_object_vars function
    $d = get_object_vars($d);
  }

  if (is_array($d)) {
    /*
		* Return array converted to object
		* Using __FUNCTION__ (Magic constant)
		* for recursive call
		*/
    return array_map(__FUNCTION__, $d);
  } else {
    // Return array
    return $d;
  }
}

function fRequest($pVar)
{
  if (isset($_GET[$pVar])) return $_GET[$pVar];
  if (isset($_POST[$pVar])) return $_POST[$pVar];
  return "";
}

function verificaLogin()
{
  session_start();
  $temPerfil = isset($_SESSION['PESSOA']['ssid']);
  if (!$temPerfil):
    session_destroy();
    header("Location: " . CFG::Root() . "index.php");
    exit;
  endif;
}

function fSetSessionLogin($result)
{
  session_start();
  $_SESSION['PESSOA']['ssid'] = session_id();
  $_SESSION['PESSOA']['email'] = $result->fields['email'];
  $_SESSION['PESSOA']['id'] = $result->fields['id'];
}

function fGetPerfil($cd = NULL)
{
  $arr = array();
  $query = "SELECT DISTINCT td.id, td.cd, td.icon, td.ds, td.url
			  FROM CD_PERSON_PROFILE cpp
		INNER JOIN TB_PROFILE_ITEM tpi ON (tpi.id_tb_profile = cpp.id_tb_profile)
		INNER JOIN TB_DASHBOARD td ON  td.id = tpi.id_tb_dashboard)
			 WHERE cpp.id_cd_person = ?";
  if (isset($cd) && !empty($cd)):
    $query .= " AND td.cd LIKE '$cd.%'";
  else:
    $query .= " AND LENGTH(td.cd) = 2";
  endif;
  $query .= " ORDER BY td.cd";
  $result = CONN::get()->Execute($query, array($_SESSION['PESSOA']['id']));
  while (!$result->EOF):
    $child = fGetPerfil($result->fields['cd']);
    $arr[$result->fields['id']] = array(
      "opt"   => utf8_encode($result->fields['ds']),
      "ico"   => $result->fields['icon'],
      "url"   => $result->fields['url'],
      "active" => false,
      "child"  => $child
    );
    $result->MoveNext();
  endwhile;
  return $arr;
}

function fSetVerificaPerfil($id_cd_person)
{
  //VERIFICA SE TEM AO MENOS UM PERFIL, SE NAO INSERE PERFIL BASICO 0-GUEST.
  $resperf = CONN::get()->Execute("SELECT * FROM CD_PERSON_PROFILE WHERE id_cd_person = ?", array($id_cd_person));
  if ($resperf->EOF):
    CONN::get()->Execute(
      "
			INSERT INTO CD_PERSON_PROFILE(
				id_cd_person,
				id_tb_profile
			) VALUES (
				?,
				?
			)",
      array($id_cd_person, 0)
    );
  endif;
}

function array_msort($array, $cols)
{
  $colarr = array();
  foreach ($cols as $col => $order) {
    $colarr[$col] = array();
    foreach ($array as $k => $row) {
      $colarr[$col]['_' . $k] = strtolower($row[$col]);
    }
  }
  $eval = 'array_multisort(';
  foreach ($cols as $col => $order) {
    $eval .= '$colarr[\'' . $col . '\'],' . $order . ',';
  }
  $eval = substr($eval, 0, -1) . ');';
  eval($eval);
  $ret = array();
  foreach ($colarr as $col => $arr) {
    foreach ($arr as $k => $v) {
      $k = substr($k, 1);
      if (!isset($ret[$k])) $ret[$k] = $array[$k];
      $ret[$k][$col] = $array[$k][$col];
    }
  }
  return $ret;
}

function fReturnStringNull($s, $default = null)
{
  if (isset($s) && trim($s) !== ""):
    return utf8_decode($s);
  endif;
  return $default;
}

function fReturnNumberNull($n, $default = null)
{
  if (isset($n) && is_numeric($n)):
    return $n;
  endif;
  return $default;
}

function getDateNull($vl)
{
  if (!isset($vl) || empty($vl) || is_null($vl)):
    return null;
  endif;
  return fStrToDate($vl, "Y-m-d");
}

function fDataFilters($param)
{
  $strFilter = "<div class=\"col-xs-8\" id=\"divFilters\" filter-to=\"" . $param["filterTo"] . "\"></div>";
  $strFilter .= "<div class=\"input-group col-xs-4 pull-right\">";
  $strFilter .= "<select class=\"selectpicker form-control input-sm\" id=\"addFilter\" onchange=\"jsFilter.addFilter(this);\" data-width=\"100%\" title=\"Adicionar filtros\" data-width=\"auto\" data-container=\"body\">";
  $arr = array_msort($param["filters"], array('label' => SORT_ASC));
  foreach ($arr as $key => $value):
    $strFilter .= "<option value=\"" . $value["value"] . "\"";
    if (isset($value["unique"])):
      $strFilter .= " data-tokens=\"unique\"";
    endif;
    $strFilter .= ">";
    $strFilter .= $value["label"] . "</option>";
  endforeach;
  $strFilter .= "</select>";
  $strFilter .= "</div>";
  $strFilter .= "<div class=\"form-group col-xs-12\"><a role=\"button\" class=\"btn btn-info btn-sm\" id=\"applyFilter\" style=\"color:#ffffff;display:none\" onclick=\"jsFilter.apply();\"><i class=\"glyphicon glyphicon-cog\"></i>&nbsp;Aplicar Filtro</a></div>";
  $strFilter .= "<br/>";
  echo $strFilter;
}

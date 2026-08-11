<?php
@require_once("../../rules/functions.php");
Session::Start();
responseMethod();

/****************************
 * Methods defined for use. *
 ****************************/
function getQueryByFilter($parameters)
{
  $where = "";
  $aWhere = array();
  if (isset($parameters["filters"])):
    $keyAnt = "";
    foreach ($parameters["filters"] as $key => $v):
      $not = false;
      if (isset($v["fg"])):
        $not = strtolower($v["fg"]) == "true";
      endif;
      $notStr = ($not ? "NOT " : "");
      if ($key == "D"):
        $where .= " AND cd.id " . $notStr . "IN";

      elseif ($key == "DI"):
        $where .= " AND hrid.seq " . $notStr . "IN";
      elseif ($key == "DA"):
        $where .= " AND hrid.seq > ";
      elseif ($key == "DE"):
        $where .= " AND hrid.seq < ";

      elseif ($key == "M"):
        $where .= " AND cm.id " . $notStr . "IN";
      elseif ($key == "MI"):
        $where .= " AND hrim.seq " . $notStr . "IN";
      elseif ($key == "MA"):
        $where .= " AND hrim.seq > ";
      elseif ($key == "ME"):
        $where .= " AND hrim.seq < ";

      else:
        $where .= " AND";
      endif;

      $prim = true;
      $where .= " (";
      if (is_array($v["vl"])):
        foreach ($v["vl"] as $value):
          if ($key == "CM"):
            if ($value == 10):
              $where .= "8,9,10";
            elseif ($value == 4):
              $where .= "4,5,6,7";
            else:
              $where .= "1,2,3";
            endif;
          elseif (empty($value)):
            $aWhere[] = "NULL";
            $where .= (!$prim ? "," : "") . "?";
          else:
            $aWhere[] = $value;
            $where .= (!$prim ? "," : "") . "?";
          endif;
          $prim = false;
        endforeach;
      elseif (empty($v["vl"])):
        $aWhere[] = "NULL";
        $where .= (!$prim ? "," : "") . "?";
      else:
        $aWhere[] = $v["vl"];
        $where .= (!$prim ? "," : "") . "?";
      endif;
      $where .= ")";
    endforeach;
  endif;

  $query = "
	SELECT DISTINCT p.id, p.nm, p.email, crd.id AS id_rd, crm.id AS id_rm
	FROM CD_PERSON p
	LEFT JOIN CON_RESULT_LAST crd ON (crd.id_cd_person = p.id AND crd.tp = 'D')
	LEFT JOIN HS_RESULT_ITEM hrid ON (hrid.id_hs_result = crd.id)
	LEFT JOIN CD_GIFTS cd ON (cd.id = hrid.id_source)  
	LEFT JOIN CON_RESULT_LAST crm ON (crm.id_cd_person = p.id AND crm.tp = 'M')
	LEFT JOIN HS_RESULT_ITEM hrim ON (hrim.id_hs_result = crm.id)
	LEFT JOIN CD_MINISTRIES cm ON (cm.id = hrim.id_source)
	WHERE 1=1 $where ORDER BY p.NM";

  //print_r($aWhere);
  // exit($query);
  return CONN::get()->Execute($query, $aWhere);
}

function getPeople($parameters)
{
  $arr = array();
  $result = getQueryByFilter($parameters);
  foreach ($result as $k => $fields):
    $arr[] = array(
      "id" => $fields["id"],
      "nm" => utf8_encode($fields["nm"]),
      "em" => $fields["email"],
      "rd" => $fields["id_rd"],
      "rm" => $fields["id_rm"]
    );
  endforeach;
  return array("result" => true, "people" => $arr);
}

function updateMember($parameters)
{
  $arr = array();
  $arr["result"] = false;

  $id = $parameters["id"];
  $vl = $parameters["val"];
  $tf = explode("-", $parameters["field"]);

  $table = mb_strtoupper($tf[0]);
  $field = mb_strtoupper($tf[1]);

  $str = "UPDATE $table SET $field = ? WHERE ID = ?";

  CONN::get()->Execute($str, array(fReturnStringNull($vl), $id));

  $arr["result"] = true;
  return $arr;
}

function insertMember($parameters)
{
  $arr = array();
  $arr["result"] = false;

  if (isset($parameters["id"]) && $parameters["id"] == "Novo"):
    if (isset($parameters["nm"])):

      CONN::get()->Execute("
				INSERT INTO CD_PERSON(nm)
        VALUES (?)
			", array($parameters["nm"]));
      $id = CONN::get()->Insert_ID();
      return getMember(array("id" => $id));
    endif;
  endif;
  return $arr;
}

function getMember($parameters)
{
  $arr = array();
  $arr["result"] = false;

  $result = CONN::get()->Execute("SELECT * FROM CD_PERSON WHERE id = ?", array($parameters["id"]));
  if (!$result->EOF):
    $arr["result"] = true;

    $arr["membro"] = array(
      "cd_pessoa-id"    => $result->fields['id'],
      "cd_pessoa-nm"    => utf8_encode(trim($result->fields['nm'])),
      "cd_pessoa-email"  => trim($result->fields['email'])
    );
  endif;
  $arr["testes"] = Testes::VerificaTestes($parameters["id"]);
  return $arr;
}

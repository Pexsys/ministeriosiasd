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
        $where .= " AND hrid.nr_item " . $notStr . "IN";
      elseif ($key == "DA"):
        $where .= " AND hrid.nr_item > ";
      elseif ($key == "DE"):
        $where .= " AND hrid.nr_item < ";

      elseif ($key == "M"):
        $where .= " AND cm.id " . $notStr . "IN";
      elseif ($key == "MI"):
        $where .= " AND hrim.nr_item " . $notStr . "IN";
      elseif ($key == "MA"):
        $where .= " AND hrim.nr_item > ";
      elseif ($key == "ME"):
        $where .= " AND hrim.nr_item < ";

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
	SELECT DISTINCT p.id, p.nm, p.cd_email, crd.id AS id_rd, crm.id AS id_rm
	FROM CD_PESSOA p
	LEFT JOIN CON_RESULTADO_LAST crd ON (crd.id_cd_pessoa = p.id AND crd.tp = 'D')
	LEFT JOIN HS_RESULT_ITEM hrid ON (hrid.id_hs_resultado = crd.id)
	LEFT JOIN CON_CD_DONS cd ON (cd.id = hrid.id_origem)  
	LEFT JOIN CON_RESULTADO_LAST crm ON (crm.id_cd_pessoa = p.id AND crm.tp = 'M')
	LEFT JOIN HS_RESULT_ITEM hrim ON (hrim.id_hs_resultado = crm.id)
	LEFT JOIN CON_CD_MINISTERIOS cm ON (cm.id = hrim.id_origem)
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
      "em" => $fields["cd_email"],
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
				INSERT INTO CD_PESSOA(nm)
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

  $result = CONN::get()->Execute("SELECT * FROM CD_PESSOA WHERE id = ?", array($parameters["id"]));
  if (!$result->EOF):
    $arr["result"] = true;

    $arr["membro"] = array(
      "cd_pessoa-id"    => $result->fields['id'],
      "cd_pessoa-nm"    => utf8_encode(trim($result->fields['nm'])),
      "cd_pessoa-cd_email"  => trim($result->fields['cd_email'])
    );
  endif;
  $arr["testes"] = Testes::VerificaTestes($parameters["id"]);
  return $arr;
}

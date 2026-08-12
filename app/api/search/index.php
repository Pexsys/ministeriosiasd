<?php
@require_once("../../rules/functions.php");
Session::Start();
responseMethod();

/****************************
 * Methods defined for use. *
 ****************************/
function getQueryByFilterGifts($parameters)
{
  $where = "";
  $aWhere = array("D");
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
        $where .= " AND hri.seq " . $notStr . "IN";
      elseif ($key == "DA"):
        $where .= " AND hri.seq > ";
      elseif ($key == "DE"):
        $where .= " AND hri.seq < ";
      else:
        $where .= " AND";
      endif;

      $prim = true;
      $where .= " (";
      if (is_array($v["vl"])):
        foreach ($v["vl"] as $value):
          if (empty($value)):
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

  //print_r($aWhere);
  //exit($where);

  if (!empty($where)):
    $query = "
		SELECT hri.seq, cr.nm, cd.ds, cd.cd
		FROM HS_RESULTS cr
		INNER JOIN HS_RESULT_ITEM hri ON (hri.id_hs_result = cr.id)
		INNER JOIN CD_GIFTS cd ON (cd.id = hri.id_source)  
		WHERE cr.TP = ? $where
		";
    return CONN::get()->Execute($query, $aWhere);
  endif;
  return null;
}

function getDons($parameters)
{
  $arr = array();
  $result = getQueryByFilterGifts($parameters);
  if (!is_null($result)):
    foreach ($result as $k => $fields) $arr[] = array(
      "nm" => $fields["nm"],
      "cd" => $fields["cd"],
      "dm" => $fields["ds"],
      "nt" => $fields["seq"]
    );
  endif;
  return array("result" => true, "dons" => $arr);
}

function getQueryByFilterMinisters($parameters)
{
  $where = "";
  $aWhere = array("M");
  if (isset($parameters["filters"])):
    foreach ($parameters["filters"] as $key => $v):
      $not = false;
      if (isset($v["fg"])):
        $not = strtolower($v["fg"]) == "true";
      endif;
      $notStr = ($not ? "NOT " : "");
      if ($key == "M"):
        $where .= " AND cm.id " . $notStr . "IN";
      elseif ($key == "MI"):
        $where .= " AND hri.seq " . $notStr . "IN";
      elseif ($key == "MA"):
        $where .= " AND hri.seq > ";
      elseif ($key == "ME"):
        $where .= " AND hri.seq < ";
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

  //print_r($aWhere);
  //exit($where);

  if (!empty($where)):
    $query = "
		SELECT hri.seq, cr.nm, cm.ds, cm.cd
		FROM HS_RESULTS cr
		INNER JOIN HS_RESULT_ITEM hri ON (hri.id_hs_result = cr.id)
		INNER JOIN CD_MINISTRIES cm ON (cm.id = hri.id_source)  
		WHERE cr.TP = ? $where
		";
    return CONN::get()->Execute($query, $aWhere);
  endif;
  return null;
}

function getMinisterios($parameters)
{
  $arr = array();
  $result = getQueryByFilterMinisters($parameters);
  if (!is_null($result)):
    foreach ($result as $k => $fields) $arr[] = array(
      "nm" => $fields["nm"],
      "cd" => $fields["cd"],
      "mn" => $fields["ds"],
      "nt" => Testes::LegendaDisposicao($fields["seq"])
    );
  endif;
  return array("result" => true, "ministers" => $arr);
}

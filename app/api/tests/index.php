<?php
@require_once("../../rules/functions.php");
Session::Start();
responseMethod();

/****************************
 * Methods defined for use. *
 ****************************/
function getDetailGift($parameters)
{
  $arr = array();
  $result = CONN::get()->Execute("SELECT * FROM CD_GIFTS WHERE id = ? ", array($parameters["id"]));
  foreach ($result as $rsitem) $arr = array(
    "ds" =>  $rsitem['ds'],
    "ex" =>  $rsitem['detail'],
    "rf" =>  $rsitem['refer'],
    "tk" =>  $rsitem['tasks']
  );
  return array("return" => true, "result" => $arr);
}

function questoesDonsDirect($parameters)
{
  $cd_ant = "";
  $tabindex = 0;
  $texto = "";
  $qst = 0;
  $result = CONN::get()->Execute("
	SELECT
		cgs.id,
		cgs.seq,
		cgs.prefix,
		cgs.ds,
		cgs.cd,
		r.cd_asw_gifts,
		o.seq AS seq_resp
	FROM CD_GIFTS_SVY cgs
	LEFT JOIN ASW_GIFTS r ON (r.gifts_svy = cgs.cd_gifts AND (r.cd_person = ? OR r.cd_person IS NULL))
	LEFT JOIN CD_GIFTS_ASW o ON (o.id = r.cd_asw_gifts)
	ORDER BY q.seq", array($parameters['id']));
  foreach ($result as $k => $f):
    ++$tabindex;
    $id = $f['id'];
    $cd = $f['cd'];
    $cd_asw_gifts = $f['cd_asw_gifts'];
    $nrSeqResp = $f["seq_resp"];

    $classField = isset($cd_asw_gifts) ? "has-success" : "has-error";
    $optionsResposta = "";
    if ($cd_ant != $cd):
      if ($cd_ant != ""):
        $texto .= "</div>";
      endif;
      $cd_ant = $cd;
      $optionsResposta = "";
      $resposta = CONN::get()->Execute("
				SELECT id, ds, seq
				FROM CD_GIFTS_ASW
				WHERE cd = ?
				ORDER BY seq", array($cd));
      foreach ($resposta as $j => $r):
        $optionsResposta .= (empty($optionsResposta) ? "" : ", ") . $r['seq'] . "=" . ucfirst($r['ds']);
      endforeach;
      $texto .= "<h6 class=\"row-title before-orange\">" .
        (is_null($f['prefix'])
          ? "$optionsResposta <b>" . substr($f['ds'], 0, 18) . "...</b>"
          : "<b>" . $f['prefix'] . "...</b> $optionsResposta") .
        "</h6><div class=\"row\">";
    endif;

    $cmb_resposta_base = (++$qst) . ":<input class=\"form-control input-xs\" name=\"questao\" id-questao=\"$id\" tabindex=\"$tabindex\" value=\"$nrSeqResp\" />";
    $texto .= "<div class=\"col-md-1 col-xs-1 col-lg-1 col-sm-1 $classField\">$cmb_resposta_base</div>";
  endforeach;
  $texto .= "</div>";
  return array("result" => Testes::RetornaTesteDonsQuantidades($parameters['id']), "questoes" => $texto);
}

function questoesDons($parameters)
{
  $arr = array();
  $optionsResposta = "";
  $cd_ant = "";
  $tabindex = 0;

  $result = CONN::get()->Execute("
	SELECT
		cgs.id,
		cgs.seq,
		cgs.prefix,
		cgs.ds,
	  cgs.cd_gifts_asw,
		r.cd_asw_gifts
	FROM CD_GIFTS_SVY cgs
	LEFT JOIN ASW_GIFTS r ON (r.gifts_svy = cgs.id AND r.cd_person = ?)
	ORDER BY cgs.seq", array($_SESSION['PESSOA']['id']));
  foreach ($result as $key => $fields):
    ++$tabindex;
    $id = $fields['id'];
    $cd = $fields['cd_gifts_asw'];
    $cd_asw_gifts = $fields['cd_asw_gifts'];
    $classField = isset($cd_asw_gifts) ? "has-success" : "has-error";

    $cmb_resposta_base = "<select class=\"input-sm\" name=\"questao\" id-questao=\"$id\" tabindex=\"$tabindex\">";
    if ($cd_ant != $cd):
      $cd_ant = $cd;
      $optionsResposta = "<option></option>";
      $resposta = CONN::get()->Execute("SELECT id, ds FROM CD_GIFTS_ASW WHERE cd = ? ORDER BY seq", array($cd));
      foreach ($resposta as $key => $value) $optionsResposta .= "<option value=\"" . $value['id'] . "\">" . $value['ds'] . "</option>";
    endif;

    if (isset($cd_asw_gifts)) $cmb_resposta_base .= str_replace("<option value=\"$cd_asw_gifts\">", "<option value=\"$cd_asw_gifts\" selected>", $optionsResposta);
    else $cmb_resposta_base .= $optionsResposta;
    $cmb_resposta_base .= "</select>";

    $texto = "<div class=\"form-group $classField\">";
    $texto .= $fields['prefix'] . "&nbsp;$cmb_resposta_base&nbsp;" . $fields['ds'];
    $texto .= "</div>";

    $arr[] = array("ds_qst" => $texto);
  endforeach;
  return array("result" => Testes::RetornaTesteDonsQuantidades($_SESSION['PESSOA']['id']), "questoes" => $arr);
}

function setRsDonsDirect($parameters)
{
  $qsID = $parameters["qs"];
  $col = $parameters["col"];

  $rs = CONN::get()->Execute("
		SELECT o.id
		FROM CD_GIFTS_SVY cgs
		INNER JOIN CD_GIFTS_ASW o ON (o.cd = cgs.cd_gifts_asw) 
		WHERE cgs.id = ? 
		  AND o.seq = ?
	", array($qsID, $col));

  return Testes::SetRsDonsPessoa($parameters["id"], $qsID, $rs->fields["id"]);
}

function setRsDons($parameters)
{
  return Testes::SetRsDonsPessoa($_SESSION['PESSOA']['id'], $parameters["id_qs"], $parameters["id_rs"]);
}

function finalizarDonsDirect($parameters)
{
  return Testes::FinalizarDonsPessoa($parameters['id']);
}

function finalizarDons($parameters)
{
  return Testes::FinalizarDonsPessoa($_SESSION['PESSOA']['id']);
}

function optionsMinisteriosCompromisso($parameters)
{
  $options = "<option value=\"\"></option>";
  $options .= "<option value=\"10\">Sim</option>";
  $options .= "<option value=\"1\">Não</option>";
  $options .= "<option value=\"5\">Talvez</option>";
  return $options;
}

function questoesMinisDirect($parameters)
{
  $tabindex = 0;
  $or = "<div class=\"panel-body\">";
  $or .= "<table class=\"table table-striped table-responsive\">
  	<thead>
      <tr>
		    <th>Código</th>
		    <th>Descrição</th>
		    <th>Nota</th>
		  </tr>
		</thead>
	<tbody>";

  $options = Testes::OptionsMinisteriosNota();

  $result = CONN::get()->Execute("
	SELECT
		m.id,
		m.cd,
		m.ds,
		r.grade
	FROM ASW_MINISTRIES r
	INNER JOIN CD_MINISTRIES m ON (m.id = r.id_cd_ministries)
	WHERE (r.cd_person = ? OR r.cd_person IS NULL)
	  AND r.grade > 0
	ORDER BY m.ds
	", array($parameters['id']));

  foreach ($result as $rsitem):
    $grade = $rsitem['grade'];
    $opt = str_replace("<option value=\"$grade\">", "<option value=\"$grade\" selected>", $options);
    $or .= Testes::TemplateMinisteriosDirect($rsitem['id'], $rsitem['cd'], $rsitem['ds'], ++$tabindex, $opt);
  endforeach;
  $or .= Testes::TemplateMinisteriosDirect("", "", "", 1, $options);
  $or .= "</tbody></table></div>";

  return array("result" => Testes::RetornaTesteMinisteriosQuantidades($parameters['id']), "questoes" => $or);
}

function getQstMiniCode($parameters)
{
  $result = CONN::get()->Execute("SELECT m.id, m.ds FROM CD_MINISTRIES m WHERE m.cd = ?", array($parameters['cd']));
  if (!$result->EOF) return array(
    "return" => true,
    "result" => array(
      "id" => $result->fields['id'],
      "ds" => $result->fields['ds']
    )
  );
  return array("return" => false);
}

function questoesMinisterios($parameters)
{
  return Testes::QuestoesMinisteriosPessoa($_SESSION['PESSOA']['id']);
}

function setRsMinisteriosDirect($parameters)
{
  Testes::SetRsMinisteriosPessoa($parameters["id_pessoa"], $parameters["id_qs"], $parameters["grade"]);

  $options = "<option value=\"\"></option>";
  for ($i = 1; $i <= 10; $i++):
    $options .= "<option value=\"$i\">$i</option>";
  endfor;
  return array("return" => true, "result" => Testes::TemplateMinisteriosDirect("", "", "", 1, $options));
}

function setRsMinisterios($parameters)
{
  return Testes::SetRsMinisteriosPessoa($_SESSION['PESSOA']['id'], $parameters["id_qs"], $parameters["grade"]);
}

function finalizarMiniDirect($parameters)
{
  return Testes::FinalizarMinisteriosPessoa($parameters['id']);
}

function finalizarMinisterios($parameters)
{
  return Testes::FinalizarMinisteriosPessoa($_SESSION['PESSOA']['id']);
}

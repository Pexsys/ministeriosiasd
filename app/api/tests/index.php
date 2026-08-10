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
  $result = CONN::get()->Execute("SELECT * FROM CD_DONS WHERE id = ? ", array($parameters["id"]));
  foreach ($result as $rsitem):
    $arr = array(
      "ds" => utf8_encode($rsitem['ds']),
      "ds_explain" => utf8_encode($rsitem['ds_explain']),
      "ds_ref_biblica" => utf8_encode($rsitem['ds_ref_biblica']),
      "ds_tarefas" => utf8_encode($rsitem['ds_tarefas'])
    );
  endforeach;
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
	LEFT JOIN ASW_GIFTS r ON (r.id_qs_gifts = cgs.id AND (r.id_cd_person = ? OR r.id_cd_person IS NULL))
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
        $optionsResposta .= (empty($optionsResposta) ? "" : ", ") . $r['seq'] . "=" . utf8_encode(ucfirst($r['ds']));
      endforeach;
      $texto .= "<h6 class=\"row-title before-orange\">" .
        (is_null($f['prefix'])
          ? "$optionsResposta <b>" . utf8_encode(substr($f['ds'], 0, 18)) . "...</b>"
          : "<b>" . utf8_encode($f['prefix']) . "...</b> $optionsResposta") .
        "</h6><div class=\"row\">";
    endif;

    $cmb_resposta_base = (++$qst) . ":<input class=\"form-control input-xs\" name=\"questao\" id-questao=\"$id\" tabindex=\"$tabindex\" value=\"$nrSeqResp\" />";
    $texto .= "<div class=\"col-md-1 col-xs-1 col-lg-1 col-sm-1 $classField\">$cmb_resposta_base</div>";
  endforeach;
  $texto .= "</div>";
  return array("result" => Testes::RetornaTesteDonsQuantidades($parameters['id']), "questoes" => $texto);
}

function questoesDons()
{
  session_start();
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
		cgs.cd,
		r.cd_asw_gifts
	FROM CD_GIFTS_SVY cgs
	LEFT JOIN ASW_GIFTS r ON (r.id_qs_gifts = cgs.id AND (r.id_cd_person = ? OR r.id_cd_person IS NULL))
	ORDER BY q.seq", array($_SESSION['PESSOA']['id']));
  while (!$result->EOF):
    ++$tabindex;
    $id = $result->fields['id'];
    $cd = $result->fields['cd'];
    $cd_asw_gifts = $result->fields['cd_asw_gifts'];
    $classField = isset($cd_asw_gifts) ? "has-success" : "has-error";

    $cmb_resposta_base = "<select class=\"input-sm\" name=\"questao\" id-questao=\"$id\" tabindex=\"$tabindex\">";
    if ($cd_ant != $cd):
      $cd_ant = $cd;
      $optionsResposta = "<option></option>";
      $resposta = CONN::get()->Execute("
			SELECT id, ds
			  FROM CD_GIFTS_ASW
			 WHERE cd = ?
			ORDER BY seq", array($cd));
      while (!$resposta->EOF):
        $optionsResposta .= "<option value=\"" . $resposta->fields['id'] . "\">" . utf8_encode($resposta->fields['ds']) . "</option>";
        $resposta->MoveNext();
      endwhile;
    endif;

    if (isset($cd_asw_gifts)):
      $cmb_resposta_base .= str_replace("<option value=\"$cd_asw_gifts\">", "<option value=\"$cd_asw_gifts\" selected>", $optionsResposta);
    else:
      $cmb_resposta_base .= $optionsResposta;
    endif;
    $cmb_resposta_base .= "</select>";

    $texto = "<div class=\"form-group $classField\">";
    $texto .= utf8_encode($result->fields['prefix']) . "&nbsp;$cmb_resposta_base&nbsp;" . utf8_encode($result->fields['ds']);
    $texto .= "</div>";

    $arr[] = array(
      "ds_qst" => $texto
    );

    $result->MoveNext();
  endwhile;
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

  return setRsDonsPessoa($parameters["id"], $qsID, $rs->fields["id"]);
}

function setRsDons($parameters)
{
  session_start();
  return setRsDonsPessoa($_SESSION['PESSOA']['id'], $parameters["id_qs"], $parameters["id_rs"]);
}

function setRsDonsPessoa($pessoaID, $qsID, $rsID)
{
  //SE RESPOSTA PREENCHIDA
  if (isset($rsID) && !empty($rsID)):
    $result = CONN::get()->Execute("SELECT * FROM ASW_GIFTS WHERE id_qs_gifts = ? AND id_cd_person = ?", array($qsID, $pessoaID));
    if ($result->EOF):
      CONN::get()->Execute("INSERT INTO ASW_GIFTS (id_cd_person, id_qs_gifts, cd_asw_gifts) VALUES (?,?,?)", array($pessoaID, $qsID, $rsID));
    else:
      CONN::get()->Execute("UPDATE ASW_GIFTS SET cd_asw_gifts = ? WHERE id_qs_gifts = ? AND id_cd_person = ?", array($rsID, $qsID, $pessoaID));
    endif;

  //SE RESPOSTA EM BRANCO
  else:
    CONN::get()->Execute("DELETE FROM ASW_GIFTS WHERE id_qs_gifts = ? AND id_cd_person = ?", array($qsID, $pessoaID));
  endif;
  return array("return" => true, "result" => Testes::RetornaTesteDonsQuantidades($pessoaID));
}


function finalizarDonsPessoa($pessoaID)
{
  $donsPend = Testes::RetornaTesteDonsQuantidades($pessoaID);

  //SE EXISTE TESTE DE DONS PENDENTE
  if ($donsPend["nr_rsp"] == $donsPend["nr_qst"]):

    //RECUPERAR REGRA DA DATA DE VALIDADE DO TESTE DE DONS.
    $dhConclusao = date('Y-m-d H:i:s');
    $dhFimValidade = Testes::CalculaValidade("TESTE_DONS", $dhConclusao);

    //INSERE CAPA DO TESTE
    CONN::get()->Execute(
      "INSERT INTO HS_RESULTS ( id_cd_person, dh_conclusion, dh_fin_valid, tp ) VALUES ( ?, ?, ?, ? )",
      array($pessoaID, $dhConclusao,  $dhFimValidade, 'D')
    );

    $id = CONN::get()->Insert_ID();

    //INSERE ITENS DO TESTE	
    CONN::get()->Execute(
      "INSERT INTO HS_RESULT_ITEM ( id_hs_result, ds, seq, id_source, cd_source ) 
			SELECT $id AS id_hs_result, res.ds, res.seq, res.id, res.cd
			FROM (SELECT t.ds AS ds, t.id, t.cd, SUM(c.nr_peso) AS seq
				FROM ASW_GIFTS r 
			  INNER JOIN CON_QS_DONS q ON (r.id_qs_gifts = q.id)
			  INNER JOIN CON_CD_DONS t ON (t.id = q.id_cd_dons)
			  INNER JOIN CD_GIFTS_ASW c ON (r.cd_asw_gifts = c.id)
			       WHERE r.id_cd_person = ?
			    GROUP BY t.ds, t.id, t.cd) res",
      array($pessoaID)
    );

    //APAGA RESPOSTAS	
    CONN::get()->Execute("DELETE FROM ASW_GIFTS WHERE id_cd_person = ?", $pessoaID);
  endif;
}

function finalizarDonsDirect($parameters)
{
  finalizarDonsPessoa($parameters['id']);
}

function finalizarDons()
{
  session_start();
  finalizarDonsPessoa($_SESSION['PESSOA']['id']);
}

function optionsMinisteriosCompromisso()
{
  $options = "<option value=\"\"></option>";
  $options .= "<option value=\"10\">Sim</option>";
  $options .= "<option value=\"1\">Não</option>";
  $options .= "<option value=\"5\">Talvez</option>";
  return $options;
}

function optionsMinisteriosNota()
{
  $options = "<option value=\"\"></option>";
  for ($i = 1; $i <= 10; $i++):
    $options .= "<option value=\"$i\">$i</option>";
  endfor;
  return $options;
}

function questoesMinisDirect($parameters)
{
  $tabindex = 0;
  $or = "<div class=\"panel-body\">";
  $or .= "<table class=\"table table-striped table-responsive\">
  		<thead><tr>
		      <th>Código</th>
		      <th>Descrição</th>
		      <th>Nota</th>
		    </tr>
		  </thead>
	<tbody>";

  $options = optionsMinisteriosNota();

  $result = CONN::get()->Execute("
	SELECT
		m.id,
		m.cd,
		m.ds,
		r.grade
	FROM ASW_MINISTRIES r
	INNER JOIN CON_CD_MINISTRIES m ON (m.id = r.id_cd_ministries)
	WHERE (r.id_cd_person = ? OR r.id_cd_person IS NULL)
	  AND r.grade > 0
	ORDER BY m.ds
	", array($parameters['id']));

  foreach ($result as $rsitem):
    $grade = $rsitem['grade'];
    $opt = str_replace("<option value=\"$grade\">", "<option value=\"$grade\" selected>", $options);

    $or .= templateMinisteriosDirect($rsitem['id'], $rsitem['cd'], utf8_encode($rsitem['ds']), ++$tabindex, $opt);
  endforeach;
  $or .= templateMinisteriosDirect("", "", "", 1, $options);

  $or .= "</tbody></table></div>";

  return array("result" => Testes::RetornaTesteMinisteriosQuantidades($parameters['id']), "questoes" => $or);
}

function getQstMiniCode($parameters)
{
  $arr = array();
  $result = CONN::get()->Execute("
	SELECT
		m.id,
		m.ds
	FROM CON_CD_MINISTRIES m
	WHERE m.cd = ?
	", array($parameters['cd']));
  if (!$result->EOF):
    return array(
      "return" => true,
      "result" => array(
        "id" => $result->fields['id'],
        "ds" => utf8_encode($result->fields['ds'])
      )
    );
  endif;
  return array("return" => false);
}

function templateMinisteriosDirect($id, $cd, $ds, $i, $opt)
{
  return "<tr>
		<td class=\"col-lg-1 col-sm-2 col-xs-3\"><input type=\"text\" name=\"cdQuestao\" value=\"$cd\" class=\"form-control input-sm\" placeholder=\"Código\"/></td>
		<td class=\"col-lg-10 col-sm-8 col-xs-6\"><span name=\"lblQuestao\">$ds</span></td>
		<td class=\"col-lg-1 col-sm-2 col-xs-3\"><select class=\"form-control input-sm\" name=\"questao\" id-questao=\"$id\" tabindex=\"$i\">$opt</select></td>
	</tr>";
}

function questoesMinisterios()
{
  session_start();
  return questoesMinisteriosPessoa($_SESSION['PESSOA']['id']);
}

function questoesMinisteriosPessoa($pessoaID)
{
  $arr = array();
  $tabindex = 0;

  $options = optionsMinisteriosNota();

  $result = CONN::get()->Execute("
	SELECT
		m.id,
		m.cd,
		m.ds,
		m.ds_cd_ministerios_gp,
		r.grade
	FROM CON_CD_MINISTRIES m
	LEFT JOIN ASW_MINISTRIES r ON (r.id_cd_ministries = m.id AND (r.id_cd_person = ? OR r.id_cd_person IS NULL))
	ORDER BY m.id_cd_ministries_gp, m.cd
	", array($pessoaID));
  foreach ($result as $rsitem):
    ++$tabindex;

    $id = $rsitem['id'];
    $grade = $rsitem['grade'];
    $cd = $rsitem['cd'];
    $ds = utf8_encode($rsitem['ds']);
    $da = utf8_encode($rsitem['ds_cd_ministerios_gp']);

    $opt = str_replace("<option value=\"$grade\">", "<option value=\"$grade\" selected>", $options);
    $arr[] = array(
      "da" => $da,
      "cd" => $cd,
      "ds" => "<div>$ds&nbsp;<select class=\"input-sm pull-right\" name=\"questao\" id-questao=\"$id\" tabindex=\"$tabindex\">$opt</select></div>"
    );
  endforeach;
  return array("result" => Testes::RetornaTesteMinisteriosQuantidades($pessoaID), "questoes" => $arr);
}

function setRsMinisteriosDirect($parameters)
{
  setRsMinisteriosPessoa($parameters["id_pessoa"], $parameters["id_qs"], $parameters["grade"]);

  $options = "<option value=\"\"></option>";
  for ($i = 1; $i <= 10; $i++):
    $options .= "<option value=\"$i\">$i</option>";
  endfor;
  return array("return" => true, "result" => templateMinisteriosDirect("", "", "", 1, $options));
}

function setRsMinisterios($parameters)
{
  session_start();
  return setRsMinisteriosPessoa($_SESSION['PESSOA']['id'], $parameters["id_qs"], $parameters["grade"]);
}

function setRsMinisteriosPessoa($pessoaID, $id, $nt)
{
  //SE RESPOSTA PREENCHIDA
  if (isset($nt) && !empty($nt)):
    $result = CONN::get()->Execute("SELECT * FROM ASW_MINISTRIES WHERE id_cd_ministries = ? AND id_cd_person = ?", array($id, $pessoaID));
    if ($result->EOF):
      CONN::get()->Execute("DELETE FROM ASW_MINISTRIES WHERE grade IS NULL AND id_cd_person = ?", array($pessoaID));
      CONN::get()->Execute("INSERT INTO ASW_MINISTRIES (id_cd_person, id_cd_ministries, grade) VALUES (?,?,?)", array($pessoaID, $id, $nt));
    else:
      CONN::get()->Execute("UPDATE ASW_MINISTRIES SET grade = ? WHERE id_cd_ministries = ? AND id_cd_person = ?", array($nt, $id, $pessoaID));
    endif;

  //SE RESPOSTA EM BRANCO
  else:
    CONN::get()->Execute("DELETE FROM ASW_MINISTRIES WHERE id_cd_ministries = ? AND id_cd_person = ?", array($id, $pessoaID));
  endif;
  return array("return" => true, "result" => Testes::RetornaTesteMinisteriosQuantidades($pessoaID));
}

function finalizarMiniDirect($parameters)
{
  finalizarMinisteriosPessoa($parameters['id']);
}

function finalizarMinisterios()
{
  session_start();
  finalizarMinisteriosPessoa($_SESSION['PESSOA']['id']);
}

function finalizarMinisteriosPessoa($pessoaID)
{
  $donsPend = Testes::RetornaTesteMinisteriosQuantidades($pessoaID);

  //SE EXISTE RESPOSTAS DE MINISTERIOS
  if ($donsPend["nr_rsp"] > 0):

    //RECUPERAR REGRA DA DATA DE VALIDADE DO TESTE DE DONS.
    $dhConclusao = date('Y-m-d H:i:s');
    $dhFimValidade = Testes::CalculaValidade("TESTE_MINISTERIOS", $dhConclusao);

    //INSERE CAPA DO TESTE
    CONN::get()->Execute(
      "INSERT INTO HS_RESULTS ( id_cd_person, dh_conclusion, dh_fin_valid, tp ) VALUES ( ?, ?, ?, ? )",
      array($pessoaID, $dhConclusao,  $dhFimValidade, 'M')
    );

    $id = CONN::get()->Insert_ID();

    //INSERE ITENS DO TESTE	
    CONN::get()->Execute(
      "INSERT INTO HS_RESULT_ITEM ( id_hs_result, ds, seq, id_source, cd_source ) 
			SELECT $id AS id_hs_result, c.ds, m.grade, c.id, c.cd
			FROM ASW_MINISTRIES m
			INNER JOIN CON_CD_MINISTRIES c ON (c.id = m.id_cd_ministries)
			WHERE m.grade IS NOT NULL
			  AND m.id_cd_person = ?",
      array($pessoaID)
    );

    //APAGA RESPOSTAS	
    CONN::get()->Execute("DELETE FROM ASW_MINISTRIES WHERE id_cd_person = ?", $pessoaID);
  endif;
}

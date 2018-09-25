<?php
@require_once("../../rules/functions.php");
Session::Start();
responseMethod();

/****************************
 * Methods defined for use. *
 ****************************/
function getDetailGift( $parameters ) {
	$arr = array();
	$result = CONN::get()->Execute("SELECT * FROM CD_DONS WHERE id = ? ", array( $parameters["id"] ) );
	foreach ($result as $rsitem):
		$arr = array( 
			"ds" => utf8_encode($rsitem['ds']),
			"ds_explain" => utf8_encode($rsitem['ds_explain']),
			"ds_ref_biblica" => utf8_encode($rsitem['ds_ref_biblica']),
			"ds_tarefas" => utf8_encode($rsitem['ds_tarefas'])
		);
	endforeach;
	return array( "return" => true, "result" => $arr );
}

function questoesDonsDirect( $parameters ){
	$cd_cd_dons_resp_ant = "";
	$tabindex = 0;
	$texto = "";
	$qst = 0;
	$result = CONN::get()->Execute("
	SELECT
		q.id,
		q.nr_seq,
		q.ds_prefixo,
		q.ds_texto,
		q.cd_cd_dons_resp,
		r.id_cd_dons_resp,
		o.nr_seq AS nr_seq_resp
	FROM CON_QS_DONS q
	LEFT JOIN RP_DONS r ON (r.id_qs_dons = q.id AND (r.id_cd_pessoa = ? OR r.id_cd_pessoa IS NULL))
	LEFT JOIN CD_DONS_RESP o ON (o.id = r.id_cd_dons_resp)
	ORDER BY q.nr_seq", array( $parameters['id'] ) );
	foreach ($result as $k => $f):
		++$tabindex;
		$id = $f['id'];
		$cd_cd_dons_resp = $f['cd_cd_dons_resp'];
		$id_cd_dons_resp = $f['id_cd_dons_resp'];
		$nrSeqResp = $f["nr_seq_resp"];
		
		$classField = isset($id_cd_dons_resp) ? "has-success" : "has-error";
		$optionsResposta = "";
		if ( $cd_cd_dons_resp_ant != $cd_cd_dons_resp ):
			if ($cd_cd_dons_resp_ant != ""):
				$texto .= "</div>";
			endif;
			$cd_cd_dons_resp_ant = $cd_cd_dons_resp;
			$optionsResposta = "";
			$resposta = CONN::get()->Execute("
				SELECT id, ds, nr_seq
				FROM CD_DONS_RESP
				WHERE cd = ?
				ORDER BY nr_seq", array($cd_cd_dons_resp) );
			foreach ($resposta as $j => $r):
				$optionsResposta .= (empty($optionsResposta) ? "" : ", " ) . $r['nr_seq']. "=". utf8_encode(ucfirst($r['ds']));
			endforeach;
			$texto .= "<h6 class=\"row-title before-orange\">".
			(is_null($f['ds_prefixo']) 
				? "$optionsResposta <b>". utf8_encode(substr($f['ds_texto'],0,18)) ."...</b>" 
				: "<b>". utf8_encode($f['ds_prefixo']) ."...</b> $optionsResposta").
			"</h6><div class=\"row\">";
		endif;

		$cmb_resposta_base = (++$qst).":<input class=\"form-control input-xs\" name=\"questao\" id-questao=\"$id\" tabindex=\"$tabindex\" value=\"$nrSeqResp\" />";
		$texto .= "<div class=\"col-md-1 col-xs-1 col-lg-1 col-sm-1 $classField\">$cmb_resposta_base</div>";
	endforeach;
	$texto .= "</div>";
	return array( "result" => Testes::RetornaTesteDonsQuantidades( $parameters['id'] ), "questoes" => $texto );
}

function questoesDons(){
	session_start();
	$arr = array();
	$optionsResposta = "";
	$cd_cd_dons_resp_ant = "";
	$tabindex = 0;

	$result = CONN::get()->Execute("
	SELECT
		q.id,
		q.nr_seq,
		q.ds_prefixo,
		q.ds_texto,
		q.cd_cd_dons_resp,
		r.id_cd_dons_resp
	FROM CON_QS_DONS q
	LEFT JOIN RP_DONS r ON (r.id_qs_dons = q.id AND (r.id_cd_pessoa = ? OR r.id_cd_pessoa IS NULL))
	ORDER BY q.nr_seq", array($_SESSION['PESSOA']['id']) );
	while (!$result->EOF):
		++$tabindex;
		$id = $result->fields['id'];
		$cd_cd_dons_resp = $result->fields['cd_cd_dons_resp'];
		$id_cd_dons_resp = $result->fields['id_cd_dons_resp'];
		$classField = isset($id_cd_dons_resp) ? "has-success" : "has-error";

		$cmb_resposta_base = "<select class=\"input-sm\" name=\"questao\" id-questao=\"$id\" tabindex=\"$tabindex\">";
		if ( $cd_cd_dons_resp_ant != $cd_cd_dons_resp ):
			$cd_cd_dons_resp_ant = $cd_cd_dons_resp;
			$optionsResposta = "<option></option>";
			$resposta = CONN::get()->Execute("
			SELECT id, ds
			  FROM CD_DONS_RESP
			 WHERE cd = ?
			ORDER BY nr_seq", array($cd_cd_dons_resp) );
			while (!$resposta->EOF):
				$optionsResposta .= "<option value=\"".$resposta->fields['id']."\">".utf8_encode($resposta->fields['ds'])."</option>";
				$resposta->MoveNext();
			endwhile;
		endif;

		if ( isset($id_cd_dons_resp) ):
			$cmb_resposta_base .= str_replace( "<option value=\"$id_cd_dons_resp\">", "<option value=\"$id_cd_dons_resp\" selected>", $optionsResposta );
		else:
			$cmb_resposta_base .= $optionsResposta;
		endif;
		$cmb_resposta_base .= "</select>";

		$texto = "<div class=\"form-group $classField\">";
		$texto .= utf8_encode($result->fields['ds_prefixo'])."&nbsp;$cmb_resposta_base&nbsp;".utf8_encode($result->fields['ds_texto']);
		$texto .= "</div>";
		
		$arr[] = array( 
			"ds_qst" => $texto
		);
		
		$result->MoveNext();
	endwhile;
	return array( "result" => Testes::RetornaTesteDonsQuantidades( $_SESSION['PESSOA']['id'] ), "questoes" => $arr );
}

function setRsDonsDirect( $parameters ){
	$qsID = $parameters["qs"];
	$col = $parameters["col"];
	
	$rs = CONN::get()->Execute("
		SELECT o.id
		FROM CON_QS_DONS q
		INNER JOIN CD_DONS_RESP o ON ( o.cd = q.cd_cd_dons_resp ) 
		WHERE q.id = ? 
		  AND o.nr_seq = ?
	", array( $qsID, $col ) );

	return setRsDonsPessoa( $parameters["id"], $qsID, $rs->fields["id"] );
}

function setRsDons( $parameters ){
	session_start();
	return setRsDonsPessoa( $_SESSION['PESSOA']['id'], $parameters["id_qs"], $parameters["id_rs"] );
}

function setRsDonsPessoa( $pessoaID, $qsID, $rsID ){
	//SE RESPOSTA PREENCHIDA
	if ( isset($rsID) && !empty($rsID) ):
		$result = CONN::get()->Execute("SELECT * FROM RP_DONS WHERE id_qs_dons = ? AND id_cd_pessoa = ?", array( $qsID, $pessoaID ) );
		if ($result->EOF):
			CONN::get()->Execute("INSERT INTO RP_DONS (id_cd_pessoa, id_qs_dons, id_cd_dons_resp) VALUES (?,?,?)", array( $pessoaID, $qsID, $rsID ) );
		else:
			CONN::get()->Execute("UPDATE RP_DONS SET id_cd_dons_resp = ? WHERE id_qs_dons = ? AND id_cd_pessoa = ?", array( $rsID, $qsID, $pessoaID ) );
		endif;
		
	//SE RESPOSTA EM BRANCO
	else:
		CONN::get()->Execute("DELETE FROM RP_DONS WHERE id_qs_dons = ? AND id_cd_pessoa = ?", array( $qsID, $pessoaID ) );
	endif;
	return array( "return" => true, "result" => Testes::RetornaTesteDonsQuantidades( $pessoaID ) );
}


function finalizarDonsPessoa( $pessoaID ){
	$donsPend = Testes::RetornaTesteDonsQuantidades( $pessoaID );

	//SE EXISTE TESTE DE DONS PENDENTE
	if ( $donsPend["nr_rsp"] == $donsPend["nr_qst"] ):
	
		//RECUPERAR REGRA DA DATA DE VALIDADE DO TESTE DE DONS.
		$dhConclusao = date('Y-m-d H:i:s');
		$dhFimValidade = Testes::CalculaValidade( "TESTE_DONS", $dhConclusao );

		//INSERE CAPA DO TESTE
		CONN::get()->Execute("INSERT INTO HS_RESULTADO ( id_cd_pessoa, dh_conclusao, dh_fim_validade, tp ) VALUES ( ?, ?, ?, ? )", 
			array( $pessoaID, $dhConclusao,  $dhFimValidade, 'D' ) );
			
		$id = CONN::get()->Insert_ID();
		
		//INSERE ITENS DO TESTE	
		CONN::get()->Execute("INSERT INTO HS_RESULT_ITEM ( id_hs_resultado, ds_item, nr_item, id_origem, cd_origem ) 
			SELECT $id AS id_hs_resultado, res.ds_item, res.nr_item, res.id, res.cd
			FROM (SELECT t.ds AS ds_item, t.id, t.cd, SUM(c.nr_peso) AS nr_item
				FROM RP_DONS r 
			  INNER JOIN CON_QS_DONS q ON (r.id_qs_dons = q.id)
			  INNER JOIN CON_CD_DONS t ON (t.id = q.id_cd_dons)
			  INNER JOIN CD_DONS_RESP c ON (r.id_cd_dons_resp = c.id)
			       WHERE r.id_cd_pessoa = ?
			    GROUP BY t.ds, t.id, t.cd) res", 
			array( $pessoaID ) );
		
		//APAGA RESPOSTAS	
		CONN::get()->Execute("DELETE FROM RP_DONS WHERE id_cd_pessoa = ?", $pessoaID );
	endif;
}

function finalizarDonsDirect( $parameters ){
	finalizarDonsPessoa( $parameters['id'] );
}

function finalizarDons() {
	session_start();
	finalizarDonsPessoa( $_SESSION['PESSOA']['id'] );
}

function optionsMinisteriosCompromisso(){
	$options = "<option value=\"\"></option>";
	$options .= "<option value=\"10\">Sim</option>";
	$options .= "<option value=\"1\">Não</option>";
	$options .= "<option value=\"5\">Talvez</option>";
	return $options;
}

function optionsMinisteriosNota(){
	$options = "<option value=\"\"></option>";
	for ($i=1;$i<=10;$i++):
		$options .= "<option value=\"$i\">$i</option>";
	endfor;
	return $options;
}

function questoesMinisDirect( $parameters ){
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
		r.nr_nota
	FROM RP_MINISTERIOS r
	INNER JOIN CON_CD_MINISTERIOS m ON (m.id = r.id_cd_ministerios)
	WHERE (r.id_cd_pessoa = ? OR r.id_cd_pessoa IS NULL)
	  AND r.nr_nota > 0
	ORDER BY m.ds
	", array( $parameters['id'] ) );
	
	foreach ($result as $rsitem):
		$nr_nota = $rsitem['nr_nota'];
		$opt = str_replace( "<option value=\"$nr_nota\">", "<option value=\"$nr_nota\" selected>", $options );
		
		$or .= templateMinisteriosDirect( $rsitem['id'], $rsitem['cd'], utf8_encode($rsitem['ds']), ++$tabindex, $opt);
	endforeach;
	$or .= templateMinisteriosDirect("","","",1,$options);
	
	$or .= "</tbody></table></div>";
	
	return array( "result" => Testes::RetornaTesteMinisteriosQuantidades( $parameters['id'] ), "questoes" => $or );
}

function getQstMiniCode( $parameters ){
	$arr = array();
	$result = CONN::get()->Execute("
	SELECT
		m.id,
		m.ds
	FROM CON_CD_MINISTERIOS m
	WHERE m.cd = ?
	", array( $parameters['cd'] ) );
	if (!$result->EOF):
		return array( "return" => true, "result" => array( 
							"id" => $result->fields['id'],
							"ds" => utf8_encode($result->fields['ds'])
							) 
			);
	endif;
	return array( "return" => false );
}

function templateMinisteriosDirect($id,$cd,$ds,$i,$opt){
	return "<tr>
		<td class=\"col-lg-1 col-sm-2 col-xs-3\"><input type=\"text\" name=\"cdQuestao\" value=\"$cd\" class=\"form-control input-sm\" placeholder=\"Código\"/></td>
		<td class=\"col-lg-10 col-sm-8 col-xs-6\"><span name=\"lblQuestao\">$ds</span></td>
		<td class=\"col-lg-1 col-sm-2 col-xs-3\"><select class=\"form-control input-sm\" name=\"questao\" id-questao=\"$id\" tabindex=\"$i\">$opt</select></td>
	</tr>";
}

function questoesMinisterios(){
	session_start();
	return questoesMinisteriosPessoa($_SESSION['PESSOA']['id']);
}

function questoesMinisteriosPessoa($pessoaID){
	$arr = array();
	$tabindex = 0;

	$options = optionsMinisteriosNota();

	$result = CONN::get()->Execute("
	SELECT
		m.id,
		m.cd,
		m.ds,
		m.ds_cd_ministerios_gp,
		r.nr_nota
	FROM CON_CD_MINISTERIOS m
	LEFT JOIN RP_MINISTERIOS r ON (r.id_cd_ministerios = m.id AND (r.id_cd_pessoa = ? OR r.id_cd_pessoa IS NULL))
	ORDER BY m.id_cd_ministerios_gp, m.cd
	", array( $pessoaID ) );
	foreach ($result as $rsitem):
		++$tabindex;
		
		$id = $rsitem['id'];
		$nr_nota = $rsitem['nr_nota'];
		$cd = $rsitem['cd'];
		$ds = utf8_encode($rsitem['ds']);
		$da = utf8_encode($rsitem['ds_cd_ministerios_gp']);
		
		$opt = str_replace( "<option value=\"$nr_nota\">", "<option value=\"$nr_nota\" selected>", $options );
		$arr[] = array(
			"da" => $da,
			"cd" => $cd,
			"ds" => "<div>$ds&nbsp;<select class=\"input-sm pull-right\" name=\"questao\" id-questao=\"$id\" tabindex=\"$tabindex\">$opt</select></div>"
		);
	endforeach;
	return array( "result" => Testes::RetornaTesteMinisteriosQuantidades( $pessoaID ), "questoes" => $arr );
}

function setRsMinisteriosDirect( $parameters ){
	setRsMinisteriosPessoa($parameters["id_pessoa"], $parameters["id_qs"], $parameters["nr_nota"]);
	
	$options = "<option value=\"\"></option>";
	for ($i=1;$i<=10;$i++):
		$options .= "<option value=\"$i\">$i</option>";
	endfor;
	return array( "return" => true, "result" => templateMinisteriosDirect("","","",1,$options) );
}

function setRsMinisterios( $parameters ){
	session_start();
	return setRsMinisteriosPessoa($_SESSION['PESSOA']['id'], $parameters["id_qs"], $parameters["nr_nota"]);
}

function setRsMinisteriosPessoa($pessoaID,$id,$nt){
	//SE RESPOSTA PREENCHIDA
	if ( isset($nt) && !empty($nt) ):
		$result = CONN::get()->Execute("SELECT * FROM RP_MINISTERIOS WHERE id_cd_ministerios = ? AND id_cd_pessoa = ?", array( $id, $pessoaID ) );
		if ($result->EOF):
			CONN::get()->Execute("DELETE FROM RP_MINISTERIOS WHERE nr_nota IS NULL AND id_cd_pessoa = ?", array( $pessoaID ) );
			CONN::get()->Execute("INSERT INTO RP_MINISTERIOS (id_cd_pessoa, id_cd_ministerios, nr_nota) VALUES (?,?,?)", array( $pessoaID, $id, $nt ) );
		else:
			CONN::get()->Execute("UPDATE RP_MINISTERIOS SET nr_nota = ? WHERE id_cd_ministerios = ? AND id_cd_pessoa = ?", array( $nt, $id, $pessoaID ) );
		endif;
		
	//SE RESPOSTA EM BRANCO
	else:
		CONN::get()->Execute("DELETE FROM RP_MINISTERIOS WHERE id_cd_ministerios = ? AND id_cd_pessoa = ?", array( $id, $pessoaID ) );
	endif;
	return array( "return" => true, "result" => Testes::RetornaTesteMinisteriosQuantidades( $pessoaID ) );
}

function finalizarMiniDirect( $parameters ){
	finalizarMinisteriosPessoa( $parameters['id'] );
}

function finalizarMinisterios() {
	session_start();
	finalizarMinisteriosPessoa($_SESSION['PESSOA']['id']);
}

function finalizarMinisteriosPessoa($pessoaID){
	$donsPend = Testes::RetornaTesteMinisteriosQuantidades( $pessoaID );

	//SE EXISTE RESPOSTAS DE MINISTERIOS
	if ( $donsPend["nr_rsp"] > 0 ):
	
		//RECUPERAR REGRA DA DATA DE VALIDADE DO TESTE DE DONS.
		$dhConclusao = date('Y-m-d H:i:s');
		$dhFimValidade = Testes::CalculaValidade( "TESTE_MINISTERIOS", $dhConclusao );

		//INSERE CAPA DO TESTE
		CONN::get()->Execute("INSERT INTO HS_RESULTADO ( id_cd_pessoa, dh_conclusao, dh_fim_validade, tp ) VALUES ( ?, ?, ?, ? )", 
			array( $pessoaID, $dhConclusao,  $dhFimValidade, 'M' ) );
			
		$id = CONN::get()->Insert_ID();
		
		//INSERE ITENS DO TESTE	
		CONN::get()->Execute("INSERT INTO HS_RESULT_ITEM ( id_hs_resultado, ds_item, nr_item, id_origem, cd_origem ) 
			SELECT $id AS id_hs_resultado, c.ds, m.nr_nota, c.id, c.cd
			FROM RP_MINISTERIOS m
			INNER JOIN CON_CD_MINISTERIOS c ON (c.id = m.id_cd_ministerios)
			WHERE m.nr_nota IS NOT NULL
			  AND m.id_cd_pessoa = ?", 
			array( $pessoaID ) );
		
		//APAGA RESPOSTAS	
		CONN::get()->Execute("DELETE FROM RP_MINISTERIOS WHERE id_cd_pessoa = ?", $pessoaID );
	endif;
}
?>

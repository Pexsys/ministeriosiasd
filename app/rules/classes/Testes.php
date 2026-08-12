<?php
class Testes
{
  public static function ExistHistorico($id, $tp)
  {
    return CONN::get()->Execute("SELECT * FROM HS_RESULTS WHERE tp = ? AND cd_person = ? ORDER BY dh_conclusion DESC", array($tp, $id));
  }

  public static function QueryResult($id)
  {
    return CONN::get()->Execute("
	    SELECT p.email, p.nm, r.dh_conclusion, r.dh_fin_valid, r.tp, i.id_source, i.ds, i.seq, i.cd_source
	      FROM HS_RESULTS r
	  INNER JOIN HS_RESULT_ITEM i ON (i.id_hs_result = r.id)
	  INNER JOIN CD_PERSON p ON (p.id = r.cd_person)
	     WHERE r.id = ?
	  ORDER BY i.seq DESC, i.ds
	  ", array($id));
  }

  public static function RetornaTesteDonsQuantidades($id)
  {
    $arr = array("nr_qst" => 0, "nr_rsp" => 0, "pc_conc" => 0);

    $qtds = CONN::get()->Execute("
	    SELECT 
	    (SELECT COUNT(*) FROM CD_GIFTS_SVY) AS nr_qst, 
	    (SELECT COUNT(*) FROM ASW_GIFTS WHERE cd_person = ?) AS nr_rsp
	  ", array($id));
    if (!$qtds->EOF):
      $arr["nr_qst"] = $qtds->fields['nr_qst'];
      $arr["nr_rsp"] = $qtds->fields['nr_rsp'];
      $arr["pc_conc"] = floor(($arr["nr_rsp"] / $arr["nr_qst"]) * 100);
    endif;
    return $arr;
  }

  public static function RetornaTesteMinisteriosQuantidades($id)
  {
    $arr = array("nr_qst" => 0, "nr_rsp" => 0);

    $qtds = CONN::get()->Execute("
	    SELECT 
	    (SELECT COUNT(*) FROM CD_MINISTRIES) AS nr_qst, 
	    (SELECT COUNT(*) FROM ASW_MINISTRIES WHERE cd_person = ?) AS nr_rsp
	  ", array($id));
    if (!$qtds->EOF):
      $arr["nr_qst"] = $qtds->fields['nr_qst'];
      $arr["nr_rsp"] = $qtds->fields['nr_rsp'];
    endif;
    return $arr;
  }

  public static function CalculaValidade($chave, $dhBaseCalculo)
  {
    $result = CONN::get()->Execute("SELECT * FROM TB_RULES WHERE ch = ? AND fg = 'S'", array("$chave|PZ_VALID"));
    if (!$result->EOF):
      $retorno = new DateTime($dhBaseCalculo);
      $vls =  explode(":", $result->fields['vl']);
      if ($vls[0] == "ANNUAL") $retorno->modify("+" . $vls[1] . " year");
      return $retorno->format('Y-m-d H:i:s');
    endif;
    return null;
  }

  public static function LegendaDisposicao($nota)
  {
    return $nota;
    // if ($nota >= 8):
    // 	return "SIM";
    // elseif ($nota >= 4):
    // 	return "TALVEZ";
    // endif;
    // return "NÃO";
  }

  public static function VerificaTestes($id)
  {
    $arr = array();
    $dh = Formatter::DateTimeNow();

    //SE NAO TEM QUESTIONARIO DE DONS EM ABERTO.
    $dons = static::RetornaTesteDonsQuantidades($id);
    if ($dons["nr_rsp"] == 0):
      //SE PASSOU DO PRAZO DE VALIDADE, ABRE AUTOMATICAMENTE NOVO TESTE.
      $result = CONN::get()->Execute("SELECT 1 FROM HS_RESULTS WHERE cd_person = ? AND dh_fin_valid > ? AND tp = ?", array($id, $dh, 'D'));
      if ($result->EOF):
        CONN::get()->Execute("INSERT INTO ASW_GIFTS(cd_person, gifts_svy) VALUES (?,?)", array($id, 1));
        $arr["dons"] = static::RetornaTesteDonsQuantidades($id);
      endif;
    else:
      $arr["dons"] = $dons;
    endif;

    //SE NAO TEM QUESTIONARIO DE MINISTERIOS EM ABERTO.
    $minis = static::RetornaTesteMinisteriosQuantidades($id);
    if ($minis["nr_rsp"] == 0):
      //SE PASSOU DO PRAZO DE VALIDADE, ABRE AUTOMATICAMENTE NOVO TESTE.
      $result = CONN::get()->Execute("SELECT 1 FROM HS_RESULTS WHERE cd_person = ? AND dh_fin_valid > ? AND tp = ?", array($id, $dh, 'M'));
      if ($result->EOF):
        CONN::get()->Execute("INSERT INTO ASW_MINISTRIES(cd_person, id_cd_ministries) VALUES (?,?)", array($id, 1));
        $arr["minis"] = static::RetornaTesteMinisteriosQuantidades($id);
      endif;
    else:
      $arr["minis"] = $minis;
    endif;

    return $arr;
  }

  public static function OptionsMinisteriosNota()
  {
    $options = "<option value=\"\"></option>";
    for ($i = 1; $i <= 10; $i++) $options .= "<option value=\"$i\">$i</option>";
    return $options;
  }

  public static function SetRsDonsPessoa($pessoaID, $qsID, $rsID)
  {
    //SE RESPOSTA PREENCHIDA
    if (isset($rsID) && !empty($rsID)):
      $result = CONN::get()->Execute("SELECT * FROM ASW_GIFTS WHERE gifts_svy = ? AND cd_person = ?", array($qsID, $pessoaID));
      if ($result->EOF):
        CONN::get()->Execute("INSERT INTO ASW_GIFTS (cd_person, gifts_svy, cd_asw_gifts) VALUES (?,?,?)", array($pessoaID, $qsID, $rsID));
      else:
        CONN::get()->Execute("UPDATE ASW_GIFTS SET cd_asw_gifts = ? WHERE gifts_svy = ? AND cd_person = ?", array($rsID, $qsID, $pessoaID));
      endif;

    //SE RESPOSTA EM BRANCO
    else:
      CONN::get()->Execute("DELETE FROM ASW_GIFTS WHERE gifts_svy = ? AND cd_person = ?", array($qsID, $pessoaID));
    endif;
    return array("return" => true, "result" => static::RetornaTesteDonsQuantidades($pessoaID));
  }

  public static function FinalizarDonsPessoa($pessoaID)
  {
    $donsPend = static::RetornaTesteDonsQuantidades($pessoaID);

    //SE EXISTE TESTE DE DONS PENDENTE
    if ($donsPend["nr_rsp"] == $donsPend["nr_qst"]):

      //RECUPERAR REGRA DA DATA DE VALIDADE DO TESTE DE DONS.
      $dhConclusao = Formatter::DateTimeNow();
      $dhFimValidade = static::CalculaValidade("TESTE_DONS", $dhConclusao);

      //INSERE CAPA DO TESTE
      CONN::get()->Execute("
      INSERT INTO HS_RESULTS (cd_person, dh_conclusion, dh_fin_valid, tp)
      VALUES (?,?,?,?)
      ", array($pessoaID, $dhConclusao, $dhFimValidade, 'D'));
      $id = CONN::get()->Insert_ID();

      //INSERE ITENS DO TESTE	
      CONN::get()->Execute("
      INSERT INTO HS_RESULT_ITEM (id_hs_result, ds, seq, id_source, cd_source) 
      SELECT $id, t.ds, t.seq, t.id_cd_gifts, SUM(c.factor)
			FROM ASW_GIFTS r 
			INNER JOIN CD_GIFTS_SVY q ON (q.id = r.gifts_svy)
			INNER JOIN CD_GIFTS t ON (t.id = q.id_cd_gifts)
			INNER JOIN CD_GIFTS_ASW c ON (r.cd_asw_gifts = c.id)
			WHERE r.cd_person = ?
			GROUP BY ds, seq, id_cd_gifts
      ", array($pessoaID));

      //APAGA RESPOSTAS	
      CONN::get()->Execute("DELETE FROM ASW_GIFTS WHERE cd_person = ?", $pessoaID);
    endif;
  }

  public static function TemplateMinisteriosDirect($id, $cd, $ds, $i, $opt)
  {
    return "<tr>
		  <td class=\"col-lg-1 col-sm-2 col-xs-3\"><input type=\"text\" name=\"cdQuestao\" value=\"$cd\" class=\"form-control input-sm\" placeholder=\"Código\"/></td>
		  <td class=\"col-lg-10 col-sm-8 col-xs-6\"><span name=\"lblQuestao\">$ds</span></td>
		  <td class=\"col-lg-1 col-sm-2 col-xs-3\"><select class=\"form-control input-sm\" name=\"questao\" id-questao=\"$id\" tabindex=\"$i\">$opt</select></td>
	  </tr>";
  }

  public static function FinalizarMinisteriosPessoa($pessoaID)
  {
    $donsPend = static::RetornaTesteMinisteriosQuantidades($pessoaID);

    //SE EXISTE RESPOSTAS DE MINISTERIOS
    if ($donsPend["nr_rsp"] > 0):

      //RECUPERAR REGRA DA DATA DE VALIDADE DO TESTE DE DONS.
      $dhConclusao = Formatter::DateTimeNow();
      $dhFimValidade = static::CalculaValidade("TESTE_MINISTERIOS", $dhConclusao);

      //INSERE CAPA DO TESTE
      CONN::get()->Execute(
        "INSERT INTO HS_RESULTS ( cd_person, dh_conclusion, dh_fin_valid, tp ) VALUES ( ?, ?, ?, ? )",
        array($pessoaID, $dhConclusao,  $dhFimValidade, 'M')
      );

      $id = CONN::get()->Insert_ID();

      //INSERE ITENS DO TESTE	
      CONN::get()->Execute(
        "INSERT INTO HS_RESULT_ITEM ( id_hs_result, ds, seq, id_source, cd_source ) 
			SELECT $id AS id_hs_result, c.ds, m.grade, c.id, c.cd
			FROM ASW_MINISTRIES m
			INNER JOIN CD_MINISTRIES c ON (c.id = m.id_cd_ministries)
			WHERE m.grade IS NOT NULL
			  AND m.cd_person = ?",
        array($pessoaID)
      );

      //APAGA RESPOSTAS	
      CONN::get()->Execute("DELETE FROM ASW_MINISTRIES WHERE cd_person = ?", $pessoaID);
    endif;
  }

  public static function QuestoesMinisteriosPessoa($pessoaID)
  {
    $arr = array();
    $tabindex = 0;

    $options = static::OptionsMinisteriosNota();

    $result = CONN::get()->Execute("
	  SELECT
		  m.id,
		  m.cd,
		  m.ds,
		  m.ds_cd_ministerios_gp,
	  	r.grade
	  FROM CD_MINISTRIES m
	  LEFT JOIN ASW_MINISTRIES r ON (r.id_cd_ministries = m.id AND (r.cd_person = ? OR r.cd_person IS NULL))
	  ORDER BY m.id_ministries_grp, m.cd
	  ", array($pessoaID));
    foreach ($result as $rsitem):
      ++$tabindex;

      $id = $rsitem['id'];
      $grade = $rsitem['grade'];
      $cd = $rsitem['cd'];
      $ds = $rsitem['ds'];
      $da = $rsitem['ds_cd_ministerios_gp'];

      $opt = str_replace("<option value=\"$grade\">", "<option value=\"$grade\" selected>", $options);
      $arr[] = array(
        "da" => $da,
        "cd" => $cd,
        "ds" => "<div>$ds&nbsp;<select class=\"input-sm pull-right\" name=\"questao\" id-questao=\"$id\" tabindex=\"$tabindex\">$opt</select></div>"
      );
    endforeach;
    return array("result" => static::RetornaTesteMinisteriosQuantidades($pessoaID), "questoes" => $arr);
  }

  public static function SetRsMinisteriosPessoa($pessoaID, $id, $nt)
  {
    //SE RESPOSTA PREENCHIDA
    if (isset($nt) && !empty($nt)):
      $result = CONN::get()->Execute("SELECT * FROM ASW_MINISTRIES WHERE id_cd_ministries = ? AND cd_person = ?", array($id, $pessoaID));
      if ($result->EOF):
        CONN::get()->Execute("DELETE FROM ASW_MINISTRIES WHERE grade IS NULL AND cd_person = ?", array($pessoaID));
        CONN::get()->Execute("INSERT INTO ASW_MINISTRIES (cd_person, id_cd_ministries, grade) VALUES (?,?,?)", array($pessoaID, $id, $nt));
      else:
        CONN::get()->Execute("UPDATE ASW_MINISTRIES SET grade = ? WHERE id_cd_ministries = ? AND cd_person = ?", array($nt, $id, $pessoaID));
      endif;

    //SE RESPOSTA EM BRANCO
    else:
      CONN::get()->Execute("DELETE FROM ASW_MINISTRIES WHERE id_cd_ministries = ? AND cd_person = ?", array($id, $pessoaID));
    endif;
    return array("return" => true, "result" => static::RetornaTesteMinisteriosQuantidades($pessoaID));
  }
}

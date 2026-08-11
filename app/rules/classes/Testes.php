<?php
class Testes
{
  public static function ExistHistorico($id, $tp)
  {
    return CONN::get()->Execute("SELECT * FROM HS_RESULTS WHERE tp = ? AND id_cd_person = ? ORDER BY dh_conclusion DESC", array($tp, $id));
  }

  public static function QueryResult($id)
  {
    return CONN::get()->Execute("
	    SELECT p.email, p.nm, r.dh_conclusion, r.dh_fin_valid, r.tp, i.id_source, i.ds, i.seq, i.cd_source
	      FROM HS_RESULTS r
	INNER JOIN HS_RESULT_ITEM i ON (i.id_hs_result = r.id)
	INNER JOIN CD_PERSON p ON (p.id = r.id_cd_person)
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
	    (SELECT COUNT(*) FROM ASW_GIFTS WHERE id_cd_person = ?) AS nr_rsp
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
	    (SELECT COUNT(*) FROM ASW_MINISTRIES WHERE id_cd_person = ?) AS nr_rsp
	", array($id));
    if (!$qtds->EOF):
      $arr["nr_qst"] = $qtds->fields['nr_qst'];
      $arr["nr_rsp"] = $qtds->fields['nr_rsp'];
    endif;
    return $arr;
  }

  public static function CalculaValidade($chave, $dhBaseCalculo)
  {
    $result = CONN::get()->Execute("SELECT * FROM TB_REGRAS WHERE ch = ? AND fg = 'S'", array("$chave|PZ_VALIDADE"));
    if (!$result->EOF):
      $retorno = new DateTime(strftime("%F %T", strtotime($dhBaseCalculo)));
      $vls =  explode(":", $result->fields['vl']);
      if ($vls[0] == "ANUAL"):
        $retorno->modify("+" . $vls[1] . " year");
      endif;
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

    //SE NAO TEM QUESTIONARIO DE DONS EM ABERTO.
    $dons = static::RetornaTesteDonsQuantidades($id);
    if ($dons["nr_rsp"] == 0):

      //SE PASSOU DO PRAZO DE VALIDADE, ABRE AUTOMATICAMENTE NOVO TESTE.
      $result = CONN::get()->Execute("SELECT 1 FROM HS_RESULTS WHERE id_cd_person = ? AND dh_fin_valid > NOW() AND tp = ?", array($id, 'D'));
      if ($result->EOF):
        CONN::get()->Execute("INSERT INTO ASW_GIFTS(id_cd_person, id_qs_gifts) VALUES (?,?) ", array($id, 1));
        $arr["dons"] = static::RetornaTesteDonsQuantidades($id);
      endif;
    else:
      $arr["dons"] = $dons;
    endif;

    //SE NAO TEM QUESTIONARIO DE MINISTERIOS EM ABERTO.
    $minis = static::RetornaTesteMinisteriosQuantidades($id);
    if ($minis["nr_rsp"] == 0):
      //SE PASSOU DO PRAZO DE VALIDADE, ABRE AUTOMATICAMENTE NOVO TESTE.
      $result = CONN::get()->Execute("SELECT 1 FROM HS_RESULTS WHERE id_cd_person = ? AND dh_fin_valid > NOW() AND tp = ?", array($id, 'M'));
      if ($result->EOF):
        CONN::get()->Execute("INSERT INTO ASW_MINISTRIES(id_cd_person, id_cd_ministries) VALUES (?,?) ", array($id, 1));
        $arr["minis"] = static::RetornaTesteMinisteriosQuantidades($id);
      endif;
    else:
      $arr["minis"] = $minis;
    endif;

    return $arr;
  }
}

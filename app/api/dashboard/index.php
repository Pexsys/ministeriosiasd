<?php
@require_once("../../rules/functions.php");
Session::Start();
responseMethod();

/****************************
 * Methods defined for use. *
 ****************************/
function downloads($parameters){
	function color($pct = 0){
		if ($pct < 50):
			return "#ff0000";
		elseif ($pct > 50):
			return "#0000ff";
		endif;
		return "#00ff00";
	}

	if ( isset($parameters["update"]) && !empty($parameters["update"]) ):
		CONN::get()->Execute("UPDATE CT_DOWNLOADS SET qt = qt+1 WHERE tp = ?", array($parameters["update"]) );
	endif;

	$rsTotal = CONN::get()->Execute("SELECT SUM(qt) AS qt FROM CT_DOWNLOADS");
	$total = $rsTotal->fields["qt"];
	$rsDons = CONN::get()->Execute("SELECT qt FROM CT_DOWNLOADS WHERE tp = 'D'");
	$dons = $rsDons->fields["qt"];
	$rsMini = CONN::get()->Execute("SELECT qt FROM CT_DOWNLOADS WHERE tp = 'M'");
	$mini = $rsMini->fields["qt"];

	$pcDons = round(($dons / $total) * 100, 0);
	$pcMini = round(($mini / $total) * 100, 0);

	return array( "downloads" => array(
		"divDons" => array( 
			"qt" => $dons * 1,
			"pc" => $pcDons,
			"cl" => color($pcDons)
		),
		"divMini" => array( 
			"qt" => $mini * 1,
			"pc" => $pcMini,
			"cl" => color($pcMini)
		)
	));
}

function painel() {
	session_start();
	$arr = array();

	//SE EXISTE TESTE DE DONS PENDENTE
	$rs = Testes::RetornaTesteDonsQuantidades( $_SESSION['PESSOA']['id'] );
	$qtdDons = 0;
	if ( $rs["nr_rsp"] > 0 ):
		$qtdDons++;
		$arr[] = array(
			"leftBkTheme" => "databox-left bg-themesecondary",
			"pc" => floor( $rs["nr_rsp"] / $rs["nr_qst"] ),
			"qt" => 1,
			"ds" => "MEU TESTE DE DONS PENDENTE",
			"rightBkTheme" => "databox-number themesecondary",
			"rightDecorate" => "databox-stat themesecondary radius-bordered",
			"ico" => "stat-icon icon-lg fa fa-tasks"
		);
	endif;
	
	//SE EXISTE TESTE DE DONS CONCLUIDOS
	$rs = Testes::ExistHistorico( $_SESSION['PESSOA']['id'], 'D' );
	if (!$rs->EOF):
		$arr[] = array(
			"leftBkTheme" => "databox-left bg-palegreen",
			"pc" => floor( ( $rs->RecordCount() / ( $rs->RecordCount() + $qtdDons ) ) * 100 ),
			"qt" => $rs->RecordCount(),
			"ds" => "MEUS TESTES DE DONS CONCLUÍDOS",
			"rightBkTheme" => "databox-number green",
			"rightDecorate" => "databox-stat bg-palegreen radius-bordered",
			"ico" => "stat-icon fa fa-check"
		);
	endif;
	
	//SE EXISTE TESTE DE MINISTERIOS PENDENTE
	$rs = Testes::RetornaTesteMinisteriosQuantidades( $_SESSION['PESSOA']['id'] );
	$qtdMinis = 0;
	if ( $rs["nr_rsp"] > 0 ):
		$qtdMinis++;
		$arr[] = array(
			"leftBkTheme" => "databox-left bg-themesecondary",
			"pc" => 0,
			"qt" => 1,
			"ds" => "MEU TESTE DE MINISTÉRIOS PENDENTE",
			"rightBkTheme" => "databox-number themesecondary",
			"rightDecorate" => "databox-stat themesecondary radius-bordered",
			"ico" => "stat-icon icon-lg fa fa-tasks"
		);
	endif;

	//SE EXISTE TESTE DE MINISTERIOS CONCLUIDOS
	$rs = Testes::ExistHistorico( $_SESSION['PESSOA']['id'], 'M' );
	if (!$rs->EOF):
		$arr[] = array(
			"leftBkTheme" => "databox-left bg-palegreen",
			"pc" => floor( ( $rs->RecordCount() / ( $rs->RecordCount() + $qtdMinis ) ) * 100 ),
			"qt" => $rs->RecordCount(),
			"ds" => "MEUS TESTES DE MINISTÉRIOS CONCLUÍDOS",
			"rightBkTheme" => "databox-number green",
			"rightDecorate" => "databox-stat bg-palegreen radius-bordered",
			"ico" => "stat-icon fa fa-check"
		);
	endif;
	
	//NUMERO DE PESSOAS CADASTRADAS
	$rs = CONN::get()->Execute("SELECT COUNT(*) AS qt FROM CD_PESSOA");
	$nrUsr = 0;
	if (!$rs->EOF):
		$nrUsr = $rs->fields["qt"];
		$arr[] = array(
			"leftBkTheme" => "databox-left bg-themeprimary",
			"pc" => 100,
			"qt" => $nrUsr,
			"ds" => "PESSOAS CADASTRADAS",
			"rightBkTheme" => "databox-number themeprimary",
			"rightDecorate" => "databox-state bg-themeprimary",
			"ico" => "fa fa-users"
		);
	endif;

	//NUMERO DE PESSOAS COM RESULTADO DE DONS
	$rs = CONN::get()->Execute("SELECT DISTINCT id_cd_pessoa FROM HS_RESULTADO WHERE tp = ?", array('D') );
	if (!$rs->EOF):
		$arr[] = array(
			"leftBkTheme" => "databox-left bg-palegreen",
			"pc" => min( ceil( ( $rs->RecordCount() / $nrUsr ) * 100 ), 100 ),
			"qt" => $rs->RecordCount(),
			"ds" => "PESSOAS COM RESULTADO DE DONS",
			"rightBkTheme" => "databox-number green",
			"rightDecorate" => "databox-stat bg-palegreen radius-bordered",
			"ico" => "stat-icon fa fa-check"
		);
	endif;

	//NUMERO DE PESSOAS COM RESULTADO DE MINISTERIOS
	$rs = CONN::get()->Execute("SELECT DISTINCT id_cd_pessoa FROM HS_RESULTADO WHERE tp = ?", array('M') );
	if (!$rs->EOF):
		$arr[] = array(
			"leftBkTheme" => "databox-left bg-palegreen",
			"pc" => min( ceil( ( $rs->RecordCount() / $nrUsr ) * 100 ), 100 ),
			"qt" => $rs->RecordCount(),
			"ds" => "PESSOAS COM RESULTADO DE MINISTÉRIOS",
			"rightBkTheme" => "databox-number green",
			"rightDecorate" => "databox-stat bg-palegreen radius-bordered",
			"ico" => "stat-icon fa fa-check"
		);
	endif;

	//TESTES DE DONS CONCLUÍDOS
	$rs = CONN::get()->Execute("SELECT * FROM HS_RESULTADO WHERE tp = ?", array('D') );
	if (!$rs->EOF):
		$arr[] = array(
			"leftBkTheme" => "databox-left bg-palegreen",
			"pc" => min( ceil( ( $rs->RecordCount() / $nrUsr ) * 100 ), 100 ),
			"qt" => $rs->RecordCount(),
			"ds" => "TESTES DE DONS CONCLUÍDOS",
			"rightBkTheme" => "databox-number green",
			"rightDecorate" => "databox-stat bg-palegreen radius-bordered",
			"ico" => "stat-icon fa fa-check"
		);
	endif;

	//TESTES DE MINISTÉRIOS CONCLUÍDOS
	$rs = CONN::get()->Execute("SELECT * FROM HS_RESULTADO WHERE tp = ?", array('M') );
	if (!$rs->EOF):
		$arr[] = array(
			"leftBkTheme" => "databox-left bg-palegreen",
			"pc" => min( ceil( ( $rs->RecordCount() / $nrUsr ) * 100 ), 100 ),
			"qt" => $rs->RecordCount(),
			"ds" => "TESTES DE MINISTÉRIOS CONCLUÍDOS",
			"rightBkTheme" => "databox-number green",
			"rightDecorate" => "databox-stat bg-palegreen radius-bordered",
			"ico" => "stat-icon fa fa-check"
		);
	endif;

	//NUMERO DE PESSOAS COM TESTES PENDENTES
	$rs = CONN::get()->Execute("SELECT DISTINCT 
		* FROM (SELECT id_cd_pessoa FROM RP_DONS UNION SELECT id_cd_pessoa FROM RP_MINISTERIOS) A");
	if (!$rs->EOF):
		$arr[] = array(
			"leftBkTheme" => "databox-left bg-themethirdcolor",
			"pc" => min( ceil( ( $rs->RecordCount() / $nrUsr ) * 100 ), 100 ),
			"qt" => $rs->RecordCount(),
			"ds" => "PESSOAS COM TESTES PENDENTES",
			"rightBkTheme" => "databox-number themethirdcolor",
			"rightDecorate" => "databox-stat themethirdcolor radius-bordered",
			"ico" => "stat-icon icon-lg fa fa-exclamation-circle"
		);
	endif;

	//NUMERO DE PESSOAS SEM TESTES
	$rs = CONN::get()->Execute("SELECT * FROM CD_PESSOA WHERE id NOT IN (SELECT id_cd_pessoa FROM HS_RESULTADO)");
	if (!$rs->EOF):
		$arr[] = array(
			"leftBkTheme" => "databox-left bg-themesecondary",
			"pc" => min( ceil( ( $rs->RecordCount() / $nrUsr ) * 100 ), 100 ),
			"qt" => $rs->RecordCount(),
			"ds" => "PESSOAS SEM NENHUM TESTE",
			"rightBkTheme" => "databox-number themesecondary",
			"rightDecorate" => "databox-stat themesecondary radius-bordered",
			"ico" => "stat-icon icon-lg fa fa-question-circle"
		);
	endif;

	return array( "panels" => $arr );
}
?>

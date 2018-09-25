<?php
@require_once("../include/functions.php");
@require_once("../include/domains.php");
responseMethod();

/****************************
 * Methods defined for use. *
 ****************************/
function getFilter( $parameters ) {
	$domain = array();

	$type = $parameters["type"];
	
	//SEXO
	if ( $type == "X" ):
		$domain = array(
			array( "value" => "F", "label" => "FEMININO" ),
			array( "value" => "M", "label" => "MASCULINO" )
		);
	
	//DONS
	elseif ( $type == "D" ):
		$domain = getDomain("CON_CD_DONS");
		
	//PONTUACAO DE DONS - DI-IGUAL, DA-MAIOR, DE-MENOR
	elseif ( $type == "DI" || $type == "DA" || $type == "DE" ):
		$domain = array();
		for ($i=1;$i<=24;$i++):
			$domain[] = array( "value" => $i, "label" => $i );
		endfor;

	//MINISTERIOS
	elseif ( $type == "M" ):
		$domain = getDomain("CON_CD_MINISTERIOS");
		
	//COMPROMISSO DE MINISTERIOS - CM
	elseif ( $type == "CM" ):
		$domain = array( 
			array( "value" => 10, "label" => "SIM" ),
			array( "value" => 1, "label" => "NÃO" ),
			array( "value" => 4, "label" => "TALVEZ" )
		);

	//NOTA DE MINISTERIOS - MI-IGUAL, MA-MAIOR, ME-MENOR
	elseif ( $type == "MI" || $type == "MA" || $type == "ME" ):
		$domain = array();
		for ($i=1;$i<=10;$i++):
			$domain[] = array( "value" => $i, "label" => $i );
		endfor;
		
	endif;

	return array( "result" => true, "domain" => $domain );
}
?>
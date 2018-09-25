<?php
@require_once("../include/functions.php");
@require_once("../assets/tcpdf/tcpdf.php");
@require_once("../rules/testes.php");

class RESULTS extends TCPDF {
	
	//lines styles
	private $stLine;
	private $stLine2;
	private $posY;
	private $lineAlt;
	private $params;
	public $ordem;
	
	function __construct() {
		parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		
		$this->stLine = array('width' => 1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$this->stLine2 = array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$this->stLine3 = array(
		    'position' => '',
		    'align' => 'C',
		    'stretch' => false,
		    'fitwidth' => true,
		    'cellfitalign' => '',
		    'border' => false,
		    'hpadding' => 'auto',
		    'vpadding' => 'auto',
		    'fgcolor' => array(0,0,0),
		    'bgcolor' => false, //array(255,255,255),
		    'text' => true,
		    'font' => 'helvetica',
		    'fontsize' => 9,
		    'stretchtext' => 0
		);

		$this->SetCreator(PDF_CREATOR);
		$this->SetAuthor('Ricardo J. Cesar');
		$this->SetSubject('pexsys.info');
		$this->SetKeywords('Dons, Serviço, Ministérios, Habilidades, Servir, Igreja Adventista do Sétimo Dia, IASD');
		$this->setImageScale(PDF_IMAGE_SCALE_RATIO);
	}

	private function legendaResultado($nota){
		return $this->params["tp"] == "M" ? Testes::LegendaDisposicao($nota) : $nota;
	}
	
	public function setResult( $fields ) {
		$this->params = $fields;
		if ( $this->params["tp"] == "D" ):
			$this->params["title"] = "Resultado do Teste de Dons";
			$this->params["column"] = "Dom";
			$this->params["result"] = "Pontuação";
		elseif ( $this->params["tp"] == "M" ):
			$this->params["title"] = "Resultado do Teste de Ministérios";
			$this->params["column"] = "Ministério";
			$this->params["result"] = "Nota";
		endif;
		$this->SetTitle($this->params["title"]);
	}

	public function Footer() {
		$this->Line(5, 288, 205, 288, $this->stLine3);
		$this->SetY(-9);
		$this->SetTextColor(90,90,90);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 6);
		//Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')
		$this->SetX(5);
		$this->Cell(40, 3, date("d/m/Y H:i:s"), 0, false, 'L');
		$this->SetX(46);
		$this->Cell(125, 3, "pexsys.info", 0, false, 'C');
		$this->SetX(172);
		$this->Cell(42, 3, "Página ". $this->getAliasNumPage() ." de ". $this->getAliasNbPages(), 0, true, 'R');
	}
	
 	public function Header() {
 		$this->setCellPaddings(0,0,0,0);
		$this->setXY(0,0);
		$this->Image("logo.jpg", 5, 5, 38, 20, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		
		$this->posY = 5;
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 18);
		$this->SetFillColor(50,50,50);
		$this->SetTextColor(255,255,255);
		$this->RoundedRect(45, $this->posY, 160, 11, 1, '1001', 'FD', $this->stLine2);
		$this->setXY(50, $this->posY);
		$this->Cell(150, 11, $this->params["title"], '', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->posY += 11;
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 9);
		$this->SetFillColor(120,120,120);
		$this->setXY(45, $this->posY);
		$this->Cell(125, 6, "Nome", 'TL', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(170, $this->posY);
		$this->Cell(35, 6, "Concluído em", 'TLR', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->posY += 6;
		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 10);
		$this->SetFillColor(255,255,255);
		$this->SetTextColor(0,0,0);
		$this->setXY(45, $this->posY);
		$this->Cell(125, 7, utf8_encode($this->params["nm"]), 'TL', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(170, $this->posY);
		$this->Cell(35, 7, strftime("%d/%m/%Y %H:%M",strtotime($this->params["dh_conclusao"])), 'TLR', 1, 'C', 1, '', 0, false, 'T', 'C');
		
		$this->posY += 7;
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 9);
		$this->SetFillColor(120,120,120);
		$this->SetTextColor(255,255,255);
		$this->setXY(45, $this->posY);
		$this->Cell(125, 6, "Email", 'TL', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(170, $this->posY);
		$this->Cell(35, 6, "Válido até", 'TLR', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->posY += 6;
		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 10);
		$this->SetFillColor(255,255,255);
		$this->SetTextColor(0,0,0);
		$this->setXY(45, $this->posY);
		$this->Cell(125, 7, $this->params["cd_email"], 'TL', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(170, $this->posY);
		$this->Cell(35, 7, is_null($this->params["dh_fim_validade"]) ? "" : strftime("%d/%m/%Y %H:%M",strtotime($this->params["dh_fim_validade"])), 'TLR', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->SetFillColor(255,255,255);
		$this->RoundedRect(45, $this->posY, 125, 8, 1, '0010', 'D', $this->stLine2);
		$this->RoundedRect(170, $this->posY, 35, 8, 1, '0100', 'D', $this->stLine2);
		$this->posY += 8;
		
		$this->posY += 2;
		$this->setXY(20,$this->posY);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 10);
		$this->SetTextColor(255,255,255);
		$this->SetFillColor(80,80,80);
		$this->setCellPaddings(1,0,1,0);
		$this->setXY(5, $this->posY);
		$this->Cell(20, 9, "Ordem", 0, false, 'C', true);

		$this->setXY(25, $this->posY);
		$this->Cell(20, 9, "Código", 0, false, 'C', true);
		
		$this->setXY(45, $this->posY);
		$this->Cell(135, 9, $this->params["column"], 0, false, 'L', true);
		
		$this->setXY(180, $this->posY);
		$this->Cell(25, 9, $this->params["result"], 0, false, 'C', true);
		$this->posY += 9;
		$this->SetTextColor(0,0,0);
	}

	public function addLine($f){
		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 9);
		$this->setCellPaddings(1,1,1,1);
		if ($this->lineAlt):
			$this->SetFillColor(240,240,240);
		else:
			$this->SetFillColor(255,255,255);
		endif;
		$this->setXY(5, $this->posY);
		$this->Cell(20, 7, ++$this->ordem, 0, false, 'C', true, false, 1);
		$this->setX(25);
		$this->Cell(20, 7, $f["cd_origem"], 0, false, 'C', true, false, 1);
		$this->setX(45);
		$this->Cell(135, 7, utf8_encode($f["ds_item"]), 0, false, 'L', true, false, 1);
		$this->setX(180);
		$this->Cell(25, 7, $this->legendaResultado($f["nr_item"]), 0, false, 'C', true, false, 1);
		$this->posY+=7;
		$this->lineAlt = !$this->lineAlt;
	}
	
	public function newPage() {
		$this->AddPage();
		$this->setCellPaddings(0,0,0,0);
		$this->SetTextColor(0,0,0);
		$this->setXY(0,0);
	}

	public function download() {
		$this->lastPage();
		$this->Output("ListagemResultado_".date('Y-m-d_H:i:s').".pdf", "I");
	}
}

$pdf = new RESULTS();


$result = fQueryResult( fRequest("id") );
if ($result->EOF):
	exit("Resultado inválido. Consulte do administrador do sistema.");
endif;

$pdf->ordem = 0;
$pdf->setResult( $result->fields );
$pdf->newPage();

foreach ( $result as $ra => $f ):
	$pdf->startTransaction();
	$start_page = $pdf->getPage();
	$pdf->addLine($f);
	if  ($pdf->getNumPages() != $start_page):
		$pdf->rollbackTransaction(true);
		$pdf->newPage();
		$pdf->addLine($f);
	else:
		$pdf->commitTransaction();     
	endif;
endforeach;

$pdf->download();
exit;
?>

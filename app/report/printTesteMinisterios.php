<?php
@require_once("../include/functions.php");
@require_once("../assets/tcpdf/tcpdf.php");

class TESTEMIN extends TCPDF
{

  //lines styles
  private $stLine;
  private $stLine2;
  private $posY;
  private $lineAlt;
  private $params;
  public $grupoAtual;

  function __construct()
  {
    parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $this->stLine = array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
    $this->stLine2 = array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 255, 255));
    $this->stLine3 = array(
      'position' => '',
      'align' => 'C',
      'stretch' => false,
      'fitwidth' => true,
      'cellfitalign' => '',
      'border' => false,
      'hpadding' => 'auto',
      'vpadding' => 'auto',
      'fgcolor' => array(255, 255, 255),
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

  private function legendaResultado($nota)
  {
    return Testes::LegendaDisposicao($nota);
  }

  public function setResult($fields)
  {
    $this->params = $fields;
    $this->params["title"] = "Teste de Ministérios";
    $this->SetTitle($this->params["title"]);
  }

  public function Footer()
  {
    $this->Line(5, 288, 205, 288, $this->stLine3);
    $this->SetY(-9);
    $this->SetTextColor(90, 90, 90);
    $this->SetFont(PDF_FONT_NAME_MAIN, 'N', 6);
    //Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')
    $this->SetX(5);
    $this->Cell(40, 3, "Teste de Ministérios - Versão: " . strftime("%d/%m/%Y", strtotime($this->params["dt_ini_valid"])), 0, false, 'L');
    $this->SetX(46);
    $this->Cell(125, 3, "pexsys.info", 0, false, 'C');
    $this->SetX(172);
    $this->Cell(42, 3, "Página " . $this->getAliasNumPage() . " de " . $this->getAliasNbPages(), 0, true, 'R');
  }

  public function headerFirstPage()
  {
    $this->Image("logo.jpg", 5, 5, 38, 20, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    $this->SetFont(PDF_FONT_NAME_MAIN, 'B', 18);
    $this->SetFillColor(50, 50, 50);
    $this->SetTextColor(255, 255, 255);
    $this->RoundedRect(45, $this->posY, 160, 11, 1, '1001', 'FD', $this->stLine);
    $this->setXY(50, $this->posY);
    $this->Cell(150, 11, $this->params["title"], '', 1, 'C', 1, '', 0, false, 'T', 'C');
    $this->posY += 11;
    $this->SetFont(PDF_FONT_NAME_MAIN, 'B', 9);
    $this->SetFillColor(120, 120, 120);
    $this->setXY(45, $this->posY);
    $this->Cell(160, 6, "Nome", 'TLR', 1, 'C', 1, '', 0, false, 'T', 'C');
    $this->posY += 6;
    $this->SetFont(PDF_FONT_NAME_MAIN, 'N', 10);
    $this->SetFillColor(255, 255, 255);
    $this->SetTextColor(0, 0, 0);
    $this->setXY(45, $this->posY);
    $this->Cell(160, 7, utf8_encode($this->params["nm"]), 'TLR', 1, 'C', 1, '', 0, false, 'T', 'C');

    $this->posY += 7;
    $this->SetFont(PDF_FONT_NAME_MAIN, 'B', 9);
    $this->SetFillColor(120, 120, 120);
    $this->SetTextColor(255, 255, 255);
    $this->setXY(45, $this->posY);
    $this->Cell(125, 6, "Email", 'TL', 1, 'C', 1, '', 0, false, 'T', 'C');
    $this->setXY(170, $this->posY);
    $this->Cell(35, 6, "Versão", 'TLR', 1, 'C', 1, '', 0, false, 'T', 'C');
    $this->posY += 6;
    $this->SetFont(PDF_FONT_NAME_MAIN, 'N', 10);
    $this->SetFillColor(255, 255, 255);
    $this->SetTextColor(0, 0, 0);
    $this->setXY(45, $this->posY);
    $this->Cell(125, 7, "", 'TL', 1, 'C', 1, '', 0, false, 'T', 'C');
    $this->SetFillColor(255, 255, 255);
    $this->setXY(170, $this->posY);
    $this->Cell(35, 8, strftime("%d/%m/%Y", strtotime($this->params["dt_ini_valid"])), 'TL', 1, 'C', 1, '', 0, false, 'T', 'C');
    $this->RoundedRect(45, $this->posY, 160, 8, 1, '0110', 'D', $this->stLine);
    $this->posY += 10;
  }

  public function Header()
  {
    $this->setCellPaddings(0, 0, 0, 0);
    $this->setXY(0, 0);
    $this->posY = 5;
    if ($this->page == 1):
      $this->headerFirstPage();
    endif;
    $this->grupoAtual = "";
  }

  public function addHeaderGrupo($f)
  {
    $this->setXY(20, $this->posY);
    $this->SetFont(PDF_FONT_NAME_MAIN, 'B', 10);
    $this->SetTextColor(255, 255, 255);
    $this->SetFillColor(0, 0, 0);
    $this->SetLineStyle($this->stLine2);
    $this->setCellPaddings(1, 0, 1, 0);
    $this->setXY(5, $this->posY);
    $this->Cell(185, 9, mb_strtoupper(utf8_encode($f["ds_cd_ministerios_gp"]), "UTF-8"), 'TB', false, 'C', true, false);
    $this->SetFont(PDF_FONT_NAME_MAIN, 'B', 8);
    // $this->setXY(160, $this->posY);
    // $this->Cell(15, 9, "SIM", 'TBL', false, 'C', true, false);
    // $this->setXY(175, $this->posY);
    // $this->Cell(15, 9, "NÃO", 'TBL', false, 'C', true, false);
    $this->setXY(190, $this->posY);
    $this->Cell(15, 9, "NOTA", 'TBLR', false, 'C', true, false);
    $this->posY += 9;
    $this->SetTextColor(0, 0, 0);
    $this->lineAlt = false;
  }

  public function addLine($f)
  {
    if ($this->grupoAtual != $f["ds_cd_ministerios_gp"]):
      $this->startTransaction();
      $start_page = $this->getPage();
      $this->addHeaderGrupo($f);
      if ($this->getNumPages() != $start_page):
        $this->rollbackTransaction(true);
        $this->newPage();
        $this->addHeaderGrupo($f);
      else:
        $this->commitTransaction();
      endif;
      $this->grupoAtual = $f["ds_cd_ministerios_gp"];
    endif;
    $this->SetFont(PDF_FONT_NAME_MAIN, 'N', 9);
    $this->setCellPaddings(1, 1, 1, 1);
    if ($this->lineAlt):
      $this->SetFillColor(240, 240, 240);
    else:
      $this->SetFillColor(255, 255, 255);
    endif;
    $this->setXY(5, $this->posY);
    $this->Cell(15, 7, $f["cd"], 0, false, 'C', true, false, 1);
    $this->setX(20);
    $this->Cell(170, 7, utf8_encode($f["ds"]), 0, false, 'L', true, false, 1);
    $this->SetFillColor(230, 230, 230);
    // $this->setX(160);
    // $this->Cell(15, 7, "", 'TBL', false, 'C', true);
    // $this->SetFillColor(215,215,215);
    // $this->setX(175);
    // $this->Cell(15, 7, "", 'TBL', false, 'C', true);
    // $this->SetFillColor(200,200,200);
    $this->setX(190);
    $this->Cell(15, 7, "", 'TBLR', false, 'C', true, false);
    $this->posY += 7;
    $this->lineAlt = !$this->lineAlt;
  }

  public function newPage()
  {
    $this->AddPage();
    $this->setCellPaddings(0, 0, 0, 0);
    $this->SetTextColor(0, 0, 0);
    $this->setXY(0, 0);
  }

  public function download($option)
  {
    $option = !isset($option) || empty($option) ? "D" : $option;
    $this->lastPage();
    $this->Output("TesteMinisterios_" . date('Y-m-d_H:i:s') . ".pdf", $option);
  }
}

$pdf = new TESTEMIN();
$result = CONN::get()->Execute("SELECT * FROM CON_CD_MINISTRIES ORDER BY id_cd_ministries_gp, cd");

$pdf->setResult($result->fields);
$pdf->newPage();
foreach ($result as $ra => $f):
  $pdf->startTransaction();
  $start_page = $pdf->getPage();
  $pdf->addLine($f);
  if ($pdf->getNumPages() != $start_page):
    $pdf->rollbackTransaction(true);
    $pdf->newPage();
    $pdf->addLine($f);
  else:
    $pdf->commitTransaction();
  endif;
endforeach;

$pdf->download(fRequest("option"));
exit;

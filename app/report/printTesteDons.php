<?php
@require_once('../rules/functions.php');
Session::Start();

class TESTEDONS extends TCPDF
{

  //lines styles
  private $stLine;
  private $stLine2;
  private $stLine3;
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
    $this->SetFooterMargin(10);
  }

  public function setResult($fields)
  {
    $this->params = $fields;
    $this->params["title"] = "Teste de Dons";
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
    $this->Cell(40, 3, "Teste de Dons", 0, false, 'L');
    $this->SetX(46);
    $this->Cell(125, 3, "pexsys.info", 0, false, 'C');
    $this->SetX(170);
    $this->Cell(42, 3, "Página " . $this->getAliasNumPage() . " de " . $this->getAliasNbPages(), '', 0, 'R');
  }

  public function Header()
  {
    $this->setCellPaddings(0, 0, 0, 0);
    $this->setXY(0, 0);
    $this->posY = 5;
    $this->Image("../../img/logo.png", 5, 5, 38, 20, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
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
    $this->Cell(160, 7, "", 'TLR', 1, 'C', 1, '', 0, false, 'T', 'C');

    $this->posY += 7;
    $this->SetFont(PDF_FONT_NAME_MAIN, 'B', 9);
    $this->SetFillColor(120, 120, 120);
    $this->SetTextColor(255, 255, 255);
    $this->setXY(45, $this->posY);
    $this->Cell(160, 6, "Email", 'TLR', 1, 'C', 1, '', 0, false, 'T', 'C');
    $this->posY += 6;
    $this->SetFont(PDF_FONT_NAME_MAIN, 'N', 10);
    $this->SetFillColor(255, 255, 255);
    $this->SetTextColor(0, 0, 0);
    $this->setXY(45, $this->posY);
    $this->Cell(125, 7, "", 'TL', 1, 'C', 1, '', 0, false, 'T', 'C');
    $this->RoundedRect(45, $this->posY, 160, 8, 1, '0110', 'D', $this->stLine);
    $this->posY += 10;
    $this->grupoAtual = "";
  }

  public function addHeaderGrupo($f)
  {
    $this->posY += 2;

    $this->SetFont(PDF_FONT_NAME_MAIN, 'B', 10);
    $this->SetFillColor(0, 0, 0);
    $this->SetTextColor(255, 255, 255);
    $this->setXY(55, $this->posY);
    $this->Cell(150, 10, "Selecione a resposta que melhor se encaixa a você para cada questão abaixo:", 0, false, 'C', true, false);
    $this->posY += 10;

    $this->SetFillColor(255, 255, 255);
    $this->SetTextColor(0, 0, 0);
    if (!empty($f["prefix"])):
      $this->SetFont(PDF_FONT_NAME_MAIN, 'B', 20);
      $this->setXY(55, $this->posY + 15);
      $this->Cell(150, 30, $f["prefix"] . (substr($f["prefix"], -1) == ":" ? "" : "..."), 'TLBR', 1, 'C', 1, '', true, false, 'C', 'M');
    endif;
    $this->posY += 7;

    $this->SetFont(PDF_FONT_NAME_MAIN, 'B', 9);
    $opcoes = CONN::get()->Execute("
			SELECT nr_peso, ds
			FROM CD_GIFTS_ASW
			WHERE cd = ?
			ORDER BY seq
		", array($f["cd"]));
    $this->posY += 23;
    $this->lineAlt = false;
    return $opcoes;
  }

  public function addLine($f, $opcoes = null)
  {
    if ($this->grupoAtual != $f["cd"]):
      $this->startTransaction();
      $start_page = $this->getPage();
      $opcoes = $this->addHeaderGrupo($f);
      if ($this->getNumPages() != $start_page):
        $this->rollbackTransaction(true);
        $this->newPage();
        $this->addHeaderGrupo($f);
      else:
        $this->commitTransaction();
      endif;

      $x = 10;
      foreach ($opcoes as $op => $fo):
        $this->setXY($x, $this->posY);
        $this->StartTransform();
        $this->Rotate(90);
        $this->Cell(40, 10, $fo["nr_peso"] . " - " . $fo["ds"], 'TLRB', 1, 'L', 1, '', 1, false, 'C', 'M');
        $this->StopTransform();
        $x += 10;
      endforeach;
      $this->SetFont(PDF_FONT_NAME_MAIN, 'N', 10);
      $this->posY += 6;
      $this->grupoAtual = $f["cd"];
    endif;
    $this->setCellPaddings(1, 1, 1, 1);
    if ($this->lineAlt):
      $this->SetFillColor(240, 240, 240);
    else:
      $this->SetFillColor(255, 255, 255);
    endif;
    $x = 5;
    foreach ($opcoes as $op => $fo):
      $this->setXY($x, $this->posY);
      $this->Cell(10, 12, "", 'TLBR', 1, 'L', 1, '', 0, false, 'C', 'M');
      $x += 10;
    endforeach;
    $this->MultiCell(150, 12, $f["ds"], 'TLBR', 'L', 1, 1, $x, $this->posY - 6, true, 0);
    $this->posY += 12;
    $this->lineAlt = !$this->lineAlt;
    return $opcoes;
  }

  public function newPage()
  {
    $this->AddPage();
    $this->setCellPaddings(1, 1, 1, 1);
    $this->SetTextColor(0, 0, 0);
    $this->setXY(0, 0);
  }

  public function download($option)
  {
    $option = !isset($option) || empty($option) ? "D" : $option;
    $this->lastPage();
    $this->Output("TesteDons_" . Formatter::DateTimeNow('Y-m-d_H:i:s') . ".pdf", $option);
  }
}

$pdf = new TESTEDONS();
$result = CONN::get()->Execute("SELECT * FROM CD_GIFTS_SVY ORDER BY cd_gifts_asw, seq");

$pdf->setResult($result->field);
$pdf->newPage();
$opcoes = null;
foreach ($result as $ra => $f):
  $pdf->startTransaction();
  $start_page = $pdf->getPage();
  $opcoes = $pdf->addLine($f, $opcoes);
  if ($pdf->getNumPages() != $start_page):
    $pdf->rollbackTransaction(true);
    $pdf->newPage();
    $pdf->addLine($f, $opcoes);
  else:
    $pdf->commitTransaction();
  endif;

endforeach;

$pdf->download(fRequest("option"));

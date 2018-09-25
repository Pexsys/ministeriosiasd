<?php

class HtmlHelper
{
  public static $STATUS_COMPRA_LIST = array(
    array("value" => 0, "text" => "RECUSADA MDA (MDA / PENDENTE REGIONAL)", "color" => "#dc3545"),
    array("value" => 1, "text" => "PENDENTE (REGIONAL)", "color" => "#ffc107"),
    array("value" => 2, "text" => "FINALIZADA (REGIONAL / PENDENTE MDA)", "color" => "#17a2b8"),
    array("value" => 3, "text" => "APROVADA (COMPRA AUTORIZADA)", "color" => "#28a745"),
    array("value" => 4, "text" => "ENCERRADA (REGIONAL)", "color" => "#6c757d"),
  );

  public static $QRCODE_TYPE = array(
    'C' => 'CLUBE',
    'P' => 'PESSOA',
    'MC' => 'MODALIDADE/CLUBE',
    'T' => 'TIPO DE TRANSPORTE INTERMODAL',
  );

  public static $TIEBREAKER_LENGTH = 7;

  public static $WF_MODAL_PENDENTE = 0;
  public static $WF_MODAL_EM_ANDAM = 1;
  public static $WF_MODAL_FINALIZA = 2;

  public static $STATUS_MODAL_DESQUALI = 0;
  public static $STATUS_MODAL_DESISTIU = 1;
  public static $STATUS_MODAL_PENDENTE = 2;
  public static $STATUS_MODAL_EM_ANDAM = 3;
  public static $STATUS_MODAL_FINALIZA = 4;

  public static $STATUS_MODAL = array(
    0 => array("value" => 0, "text" => "Desqualificado", "full" => "Clube Desqualificado", "status" => "DESQUALIFICADO"),
    1 => array("value" => 1, "text" => "Desistiu", "full" => "Participação Cancelada", "status" => "DESISTIU"),
    2 => array("value" => 2, "text" => "Pendente", "full" => "Aguardando início", "status" => "PENDENTE"),
    3 => array("value" => 3, "text" => "Em Andamento", "full" => "Participando", "status" => "PARTICIPANDO"),
    4 => array("value" => 4, "text" => "Finalizada", "full" => "Paticipação Concluída", "status" => "FINALIZADA"),
  );

  public static $STATUS_SURVEY_REGRET = 'REGRET';
  public static $STATUS_SURVEY_STARTED = 'STARTED';
  public static $STATUS_SURVEY_MAKING = 'MAKING';
  public static $STATUS_SURVEY_DONE = 'DONE';
  public static $STATUS_SURVEY_DISQUALIFIED = 'DISQUALIFIED';

  public static $REQUEST_TYPE = array(
    "ELOGIO" => array("text" => "ELOGIO", "icon" => "fas fa-hands-clapping text-success"),
    "RECLAMAÇÃO" => array("text" => "RECLAMAÇÃO", "icon" => "fas fa-triangle-exclamation text-danger"),
    "REVISÃO" => array("text" => "REVISÃO", "icon" => "fas fa-magnifying-glass text-purple"),
    "SUGESTÃO" => array("text" => "SUGESTÃO", "icon" => "fas fa-face-smile-wink text-orange"),
  );

  public static $REPORTS_TYPE = array(
    array("ds" => "ETIQUETAS", "id" => "LABELS"),
    array("ds" => "FICHA DE ATENTIMENTO DE SAÚDE", "id" => "HEALTH-BOARD"),
    array("ds" => "LISTA DE PASSAGEIROS", "id" => "PASSENGERS"),
    array("ds" => "QRCODES DOS DIRETORES", "id" => "QRCODES"),
  );

  public static $STATUS_REVISION = array(
    "ANALYSE" => array("text" => "EM ANÁLISE", "back" => "bg-info"),
    "CLOSED" => array("text" => "ENCERRADA", "back" => "bg-secondary"),
    "PENDING" => array("text" => "PENDENTE", "back" => "bg-warning"),
  );

  public static function SelectInput($name, $options, $firstEmpty = "", $selectedValue = null, $attributes = " data-live-search='true' data-live-search-normalize='true' data-size='10' data-show-subtext='true'", $f = array("group_option" => "group_option", "value" => "value", "subtext" => "subtext", "tokens" => "tokens", "text" => "text"))
  {
    $lastGroup = null;
    $html = "<select class='selectpicker form-control' name='$name' id='$name'$attributes>";
    if (!empty($firstEmpty)) $html .= "<option value=''>$firstEmpty</option>";
    foreach ($options as $key => $option) {
      if (isset($f["group_option"]) && isset($option[$f["group_option"]])) {
        if ($option[$f["group_option"]] !== $lastGroup) {
          if ($lastGroup !== null) $html .= "</optgroup>";
          $html .= "<optgroup label='" . $option[$f["group_option"]] . "'>";
          $lastGroup = $option[$f["group_option"]];
        }
      }
      $selected = ($option[$f["value"]] == $selectedValue ? " selected" : "");
      $subtext = (isset($f["subtext"]) && !empty($option[$f["subtext"]]) ? " data-subtext='" . $option[$f["subtext"]] . "'" : "");
      $tokens = (isset($f["tokens"]) && !empty($option[$f["tokens"]]) ? " data-tokens='" . $option[$f["tokens"]] . "'" : "");
      $content = (isset($f["content"]) && !empty($option[$f["content"]]) ? " data-content='" . $option[$f["content"]] . "'" : "");
      $html .= "<option value='" . $option[$f["value"]] . "'$selected$subtext$tokens$content>" . $option[$f["text"]] . "</option>";
    }
    if (isset($lastGroup)) $html .= "</optgroup>";
    return $html . "</select>";
  }

  public static function TpLogTraffic($name, $firstEmpty = "", $selectedValue = null, $attributes = " data-live-search='true' data-live-search-normalize='true' data-size='10' data-show-subtext='true'")
  {
    $options = array(
      //clubes
      array("value" => "CM", "text" => "TRIAGEM (MAKER)"),
      array("value" => "CC", "text" => "CHECKIN (CHECKER)"),
      array("value" => "LF", "text" => "CHECKOUT"),
      //veiculos
      array("value" => "ON", "text" => "ONIBUS"),
      array("value" => "CA", "text" => "CAMINHÃO / CARRETA"),
      array("value" => "UT", "text" => "UTILITÁRIO"),
      array("value" => "VE", "text" => "VEÍCULOS"),
    );
    return static::SelectInput($name, $options, $firstEmpty, $selectedValue, $attributes);
  }

  public static function TpLogLista($name, $firstEmpty = "", $selectedValue = null, $attributes = " data-live-search='true' data-live-search-normalize='true' data-size='10' data-show-subtext='true'")
  {
    $options = array(
      array("value" => "C", "text" => "CRIAÇÃO"),
      array("value" => "F", "text" => "FINALIZADA"),
      array("value" => "R", "text" => "REPROVADA"),
      array("value" => "E", "text" => "ENCERRADA"),
      array("value" => "U", "text" => "ATUALIZOU ITENS"),
      array("value" => "D", "text" => "APAGOU ITENS"),
      array("value" => "I", "text" => "INSERIU ITENS"),
    );
    return static::SelectInput($name, $options, $firstEmpty, $selectedValue, $attributes);
  }

  public static function TpLogStatusCompra($name, $firstEmpty = "", $selectedValue = null)
  {
    return static::SelectInput($name, static::$STATUS_COMPRA_LIST, $firstEmpty, $selectedValue);
  }

  public static function TpLogStatusModal($name, $firstEmpty = "", $selectedValue = null)
  {
    return static::SelectInput($name, static::$STATUS_MODAL, $firstEmpty, $selectedValue);
  }

  public static function IS_LOCALHOST()
  {
    return ($_SERVER['HTTP_HOST'] == 'localhost');
  }

  public static function EchoContentReader($title, $muted = "")
  {
    echo "<div class=\"content-header\">
      <div class=\"container-fluid\">
        <div class=\"row mb-2\">
          <div class=\"col-sm-8 col-12\">
            <h1 class=\"m-0\"><i class=\"" . Session::User('ico') . "\"></i>&nbsp;&nbsp;$title</h1>
            <h6 class=\"text-muted\">$muted</h6>
          </div>
          <div class=\"col-sm-4 col-12\"><ol class=\"breadcrumb float-sm-right\"></ol></div>
        </div>
      </div>
    </div>";
  }
}

<?php
class Formatter
{
  public static function NumBr($n, $d = 2)
  {
    return number_format(static::EmptyOr($n, 0), $d, ",", ".");
  }

  public static function DateMask($dh, $fmt = 'Y-m-d H:i:s')
  {
    return !static::Empty($dh)
      ? (new DateTime($dh, $GLOBALS['TIME_ZONE']))->format($fmt)
      : "";
  }

  public static function DateTimeNow($fmt = 'Y-m-d H:i:s')
  {
    return static::DateMask('NOW', $fmt);
  }

  public static function DateToday($fmt = 'Y-m-d')
  {
    return static::DateTimeNow($fmt);
  }

  public static function UpperCase($str, $cleanSpaces = true)
  {
    $upper = mb_strtoupper(static::EmptyOr($str, ""), "UTF-8");
    return $cleanSpaces ? static::CleanSpaces($upper) : $upper;
  }

  public static function LowerCase($str)
  {
    return static::CleanSpaces(mb_strtolower(static::EmptyOr($str, ""), "UTF-8"));
  }

  public static function TitleCase(
    $string = "",
    $delimiters = array(" ", "-", ".", "'", "O'", "D'", "O`", "Mc"),
    $exceptions = array("a", "e", "da", "de", "do", "na", "no", "em", "das", "dos", "ao", "aos", "com", "SP", "RPSP", "OU", "GPS", "COU", "DBV", "AVT", "APS", "AP", "APO", "APSE", "APSO", "APL", "APV", "APAC", "DSA", "UCB", "MDA", "P", "PP", "G", "GG", "M", "GGX", "3G", "I", "II", "III", "IV", "V", "VI")
  ) {
    $string = mb_convert_case(static::EmptyOr($string, ""), MB_CASE_TITLE, "UTF-8");
    foreach ($delimiters as $dlnr => $delimiter) :
      $words = explode($delimiter, $string);
      $newwords = array();
      foreach ($words as $wordnr => $word) :
        if (in_array(mb_strtoupper($word, "UTF-8"), $exceptions)) :
          $word = mb_strtoupper($word, "UTF-8");
        elseif (in_array(mb_strtolower($word, "UTF-8"), $exceptions)) :
          $word = mb_strtolower($word, "UTF-8");
        elseif (!in_array($word, $exceptions)) :
          $word = ucfirst($word);
        endif;
        array_push($newwords, $word);
      endforeach;
      $string = join($delimiter, $newwords);
    endforeach;
    return $string;
  }

  public static function Phone($n)
  {
    if (!static::Empty($n) && $n === 0) return "";
    $n = preg_replace('/[^0-9]/', '', $n);
    if (strlen($n) == 11) {
      return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $n);
    } elseif (strlen($n) == 10) {
      return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $n);
    } else {
      return $n;
    }
  }

  public static function StrZero($n, $q)
  {
    return ($n < 0) ? '-' . str_pad($n, $q - 1, "0", STR_PAD_LEFT) : str_pad($n, $q, "0", STR_PAD_LEFT);
  }

  public static function SafeValue($row, $col)
  {
    return (!isset($row[$col]) ? null : $row[$col]);
  }

  public static function Empty($a)
  {
    return (!isset($a) || is_null($a) || empty($a));
  }

  public static function EmptyOr($a, $b)
  {
    return !static::Empty($a) ? $a : $b;
  }

  public static function CleanSpaces($bn)
  {
    return trim(preg_replace('/\s\s+/i', " ", $bn));
  }

  public static function ClearBN($bn)
  {
    return preg_replace('/[.\-\:\/\s]/i', "", $bn);
  }

  public static function CPF($cpf)
  {
    return (!static::Empty($cpf) && $cpf !== 0)
      ? static::StrFormat("###.###.###-##", static::StrZero(static::ClearBN($cpf), 11))
      : "";
  }

  public static function StrFormat($mask, $str, $ch = '#')
  {
    $c = 0;
    $rs = '';
    for ($i = 0; $i < strlen($mask); $i++) {
      if ($mask[$i] == $ch) {
        $rs .= $str[$c];
        $c++;
      } else {
        $rs .= $mask[$i];
      }
    }
    return $rs;
  }

  public static function ReplaceAccents($str)
  {
    if (static::Empty($str)) return '';
    $str = htmlentities($str, ENT_COMPAT, "UTF-8");
    $str = preg_replace('/&([a-zA-Z])(uml|acute|grave|circ|tilde|cedil);/', '$1', $str);
    return html_entity_decode($str);
  }

  public static function Pct4Colors($qtd, $qtdTotal)
  {
    $pct = ($qtd == 0 ? 0 : floor(($qtdTotal / $qtd) * 100));
    $color = "#28a745";
    if ($pct < 50) :
      $color = "#dc3545";
    elseif ($pct < 75) :
      $color = "#ff851b";
    elseif ($pct < 100) :
      $color = "#17a2b8";
    endif;
    return array('pct' => $pct, 'color' => $color);
  }

  public static function ArrayList($list)
  {
    return array("result" => true, "list" => $list);
  }

  public static function FromScreen($parameters)
  {
    return objectToArray(json_decode(base64_decode(strtr($parameters, ' ', '+'))));
  }

  public static function UTF8Decode($value)
  {
    return  mb_convert_encoding($value, 'ISO-8859-1', 'UTF-8');
  }

  private static function LumenFactorCalc($f)
  {
    $c = $f / 255;
    return ($c <= 0.03928) ? ($c / 12.92) : (($c + 0.055) / 1.055) ** 2.4;
  }

  public static function HexToRgba($hex, $opacity)
  {
    $hex = str_replace("#", "", $hex);
    $rgbR = hexdec(substr($hex, 0, 2));
    $rgbG = hexdec(substr($hex, 2, 2));
    $rgbB = hexdec(substr($hex, 4, 2));
    return "rgba({$rgbR},{$rgbG},{$rgbB},{$opacity})";
  }

  public static function HexToRgb($hex, $n = true)
  {
    $hex = str_replace("#", "", $hex);
    $rgb = array(
      "r" => hexdec(substr($hex, 0, 2)),
      "g" => hexdec(substr($hex, 2, 2)),
      "b" => hexdec(substr($hex, 4, 2)),
    );
    $l = 0.2126 * static::LumenFactorCalc($rgb['r']) + 0.7152 * static::LumenFactorCalc($rgb['g']) + 0.0722 * static::LumenFactorCalc($rgb['b']);
    $rgb['l'] = ($l > 0.5 ? 0 : 255);
    return $n ? $rgb : array("hex" => "#$hex", "l" => "#" . static::StrZero(dechex($rgb['l']), 2) . static::StrZero(dechex($rgb['l']), 2) . static::StrZero(dechex($rgb['l']), 2));
  }

  public static function CreateAcronym($str, $sep = '')
  {
    $words = explode(' ', $str);
    $acronym = '';
    foreach ($words as $word):
      if (!empty($word)) $acronym .= strtoupper($word[0]) . $sep;
    endforeach;
    return $acronym;
  }

  public static function TruncateStr($str, $maxLength, $ellipsis = '...')
  {
    if (strlen($str) <= $maxLength) return $str;
    $truncatedString = substr($str, 0, $maxLength);
    $lastSpace = strrpos($truncatedString, ' ');
    if ($lastSpace !== false) $truncatedString = substr($truncatedString, 0, $lastSpace);
    return $truncatedString . $ellipsis;
  }

  public static function Percentual(float $pontos, float $max): float
  {
    return $max > 0 ? round(($pontos / $max) * 100, 1) : 0;
  }

  public static function Normalizer(float $obtido, float $max): float
  {
    return $max > 0 ? round(($obtido / $max) * 100, 2) : 0;
  }

  public static function Media(array $valores): float
  {
    return count($valores) ? round(array_sum($valores) / count($valores), 2) : 0;
  }

  public static function StandardDeviation(array $valores): float
  {
    $n = count($valores);
    if ($n === 0) return 0;
    $m = array_sum($valores) / $n;
    $soma = 0;
    foreach ($valores as $v) $soma += pow($v - $m, 2);
    return round(sqrt($soma / $n), 2);
  }

  public static function DashboardStatusClub(float $media, float $evolucao): string
  {
    if ($media >= 85 && $evolucao >= 0) return 'MODELO';
    if ($media >= 70) return 'ATENCAO';
    return 'PRIORITARIO';
  }

  public static function DashboardProfileClub(float $desvio): string
  {
    if ($desvio < 8) return 'EQUILIBRADO';
    if ($desvio > 18) return 'ESPECIALISTA';
    return 'DESEQUILIBRADO';
  }
}

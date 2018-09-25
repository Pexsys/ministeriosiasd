<?php
error_reporting(HtmlHelper::IS_LOCALHOST() ? E_ALL : (E_ALL & ~E_NOTICE & ~E_DEPRECATED));
ini_set('display_errors', HtmlHelper::IS_LOCALHOST() ? 1 : 0);
ini_set('memory_limit', '200M');
date_default_timezone_set('America/Sao_Paulo');
setlocale(LC_ALL, "pt_BR", "pt_BR.iso-8859-1", "pt_BR.utf-8", "portuguese");
$GLOBALS['TIME_ZONE'] = new DateTimeZone('America/Sao_Paulo');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

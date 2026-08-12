<?php
@require_once("../rules/functions.php");
Session::Start();
Session::LoginVerify();

$result = CONN::get()->Execute("SELECT * FROM CD_PERSON WHERE id = ?", array($_SESSION['PESSOA']['id']));
$pessoa = $result->fields;

$GLOBALS["breadCrumb"] = "";

function fSetActive($perfil, $id = NULL)
{
  foreach ($perfil as $key => $value):
    if (count($value["child"]) == 0 && (empty($id) || $key == $id)):
      $perfil[$key]["active"] = true;
      return $perfil;
    elseif (count($value["child"]) > 0):
      $active = false;
      $aux = fSetActive($value["child"], $id);
      foreach ($aux as $k => $v):
        if ($aux[$k]["active"]):
          $perfil[$key]["active"] = true;
          $active = true;
          break;
        endif;
      endforeach;
      $perfil[$key]["child"] = $aux;
      if ($active):
        $GLOBALS["breadCrumb"] .= " / " . $perfil[$key]["opt"];
        return $perfil;
      endif;
    endif;
  endforeach;
  return $perfil;
}

$arvore = Profile::Get();
$perfil = fSetActive($arvore, fRequest("id"));
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" ng-app="angular-app" lang="pt-br" dir="ltr">
<head>
  <meta charset="utf-8" />
  <title>Dashboard</title>
  <meta name="description" content="Dashboard" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <link rel="shortcut icon" href="<?= CFG::Root(); ?>img/favico.png" type="image/x-icon">
  <link id="bootstrap-rtl-link" href="" rel="stylesheet" />
  <link id="skin-link" href="" rel="stylesheet" type="text/css" />
  <link href="<?= CFG::Root(); ?>assets/css/bootstrap.min.css" rel="stylesheet" />
  <link href="<?= CFG::Root(); ?>assets/css/font-awesome.min.css" rel="stylesheet" />
  <link href="<?= CFG::Root(); ?>assets/css/weather-icons.min.css" rel="stylesheet" />
  <link href="http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,400,600,700,300" rel="stylesheet" type="text/css">
  <link href='http://fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>
  <link href="<?= CFG::Root(); ?>assets/css/beyond.min.css" rel="stylesheet" type="text/css" />
  <link href="<?= CFG::Root(); ?>assets/css/demo.min.css" rel="stylesheet" />
  <link href="<?= CFG::Root(); ?>assets/css/typicons.min.css" rel="stylesheet" />
  <link href="<?= CFG::Root(); ?>assets/css/animate.min.css" rel="stylesheet" />
  <link href="<?= CFG::Root(); ?>assets/css/bootstrap-select.min.css" rel="stylesheet" />
  <link href="<?= CFG::Root(); ?>assets/css/dataTables.bootstrap.css" rel="stylesheet" />
  <script src="<?= CFG::Root(); ?>assets/js/jquery.min.js"></script>
  <script src="<?= CFG::Root(); ?>assets/js/skins.min.js"></script>
  <script src="<?= CFG::Root(); ?>assets/js/angular.min.js"></script>
  <script src="<?= CFG::Root(); ?>assets/js/bootstrap.min.js"></script>
  <script src="<?= CFG::Root(); ?>assets/js/bootstrap-select.min.js"></script>
  <script src="<?= CFG::Root(); ?>assets/js/bootstrap-dialog.min.js"></script>
  <script src="<?= CFG::Root(); ?>assets/js/formValidation/formValidation.min.js"></script>
  <script src="<?= CFG::Root(); ?>assets/js/formValidation/bootstrap.min.js"></script>
  <script src="<?= CFG::Root(); ?>assets/js/slimscroll/jquery.slimscroll.min.js"></script>
  <script src="<?= CFG::Root(); ?>app/js/functions.lib.js?<?= microtime(); ?>"></script>
  <script>jsLIB.rootDir = '<?= CFG::Root(); ?>';</script>
  <script src="<?= CFG::Root(); ?>app/js/dashboard.js?<?= microtime(); ?>"></script>
</head>
<body>
  <div class="loading-container">
    <div class="loader"></div>
  </div>
  <div class="navbar">
    <div class="navbar-inner">
      <div class="navbar-container">
        <div class="navbar-header pull-left">
          <a href="#" class="navbar-brand">
            <small>
              <img src="<?= CFG::Root(); ?>img/logo.png" alt="" width="80px" height="43px" />
            </small>
          </a>
        </div>
        <div class="sidebar-collapse" id="sidebar-collapse">
          <i class="collapse-icon fa fa-bars"></i>
        </div>
        <div class="navbar-header pull-right">
          <div class="navbar-account">
            <ul class="account-area">
              <li>
                <a class="login-area dropdown-toggle" data-toggle="dropdown">
                  <section>
                    <h2><span class="profile"><span><?= ucwords(mb_strtolower($pessoa['nm'])); ?></span></span></h2>
                  </section>
                </a>
                <ul class="pull-right dropdown-menu dropdown-arrow dropdown-login-area">
                  <li class="email"><a><?= strtolower($pessoa['email']); ?></a></li>
                  <li class="theme-area">
                    <ul class="colorpicker" id="skin-changer">
                      <li><a class="colorpick-btn" href="#" style="background-color:#5DB2FF;" rel="<?= CFG::Root(); ?>assets/css/skins/blue.min.css"></a></li>
                      <li><a class="colorpick-btn" href="#" style="background-color:#2dc3e8;" rel="<?= CFG::Root(); ?>assets/css/skins/azure.min.css"></a></li>
                      <li><a class="colorpick-btn" href="#" style="background-color:#03B3B2;" rel="<?= CFG::Root(); ?>assets/css/skins/teal.min.css"></a></li>
                      <li><a class="colorpick-btn" href="#" style="background-color:#53a93f;" rel="<?= CFG::Root(); ?>assets/css/skins/green.min.css"></a></li>
                      <li><a class="colorpick-btn" href="#" style="background-color:#FF8F32;" rel="<?= CFG::Root(); ?>assets/css/skins/orange.min.css"></a></li>
                      <li><a class="colorpick-btn" href="#" style="background-color:#cc324b;" rel="<?= CFG::Root(); ?>assets/css/skins/pink.min.css"></a></li>
                      <li><a class="colorpick-btn" href="#" style="background-color:#AC193D;" rel="<?= CFG::Root(); ?>assets/css/skins/darkred.min.css"></a></li>
                      <li><a class="colorpick-btn" href="#" style="background-color:#8C0095;" rel="<?= CFG::Root(); ?>assets/css/skins/purple.min.css"></a></li>
                      <li><a class="colorpick-btn" href="#" style="background-color:#0072C6;" rel="<?= CFG::Root(); ?>assets/css/skins/darkblue.min.css"></a></li>
                      <li><a class="colorpick-btn" href="#" style="background-color:#585858;" rel="<?= CFG::Root(); ?>assets/css/skins/gray.min.css"></a></li>
                      <li><a class="colorpick-btn" href="#" style="background-color:#474544;" rel="<?= CFG::Root(); ?>assets/css/skins/black.min.css"></a></li>
                      <li><a class="colorpick-btn" href="#" style="background-color:#001940;" rel="<?= CFG::Root(); ?>assets/css/skins/deepblue.min.css"></a></li>
                    </ul>
                  </li>
                  <li class="dropdown-footer">
                    <a href="#" id="myBtnLogout">Sair</a>
                  </li>
                </ul>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="main-container container-fluid">
    <div class="page-container">
      <div class="page-sidebar" id="sidebar">
        <ul class="nav sidebar-menu">
          <!-- <div class="sidebar-header-wrapper">
						<input type="text" class="searchinput">
						<i class="searchicon fa fa-search"></i>
						<div class="searchhelper"></div>
					</div> -->
          <?php $activeOpt = Menu::Monta($perfil); ?>
        </ul>
      </div>
      <div class="page-content">
        <div class="page-breadcrumbs">
          <ul class="breadcrumb">
            <li>
              <i class="fa fa-home"></i>
              <a href="<?= CFG::Root(); ?>app/view/dashboard.php">Home</a>
            </li>
            <?= $GLOBALS["breadCrumb"]; ?>
          </ul>
        </div>
        <div class="page-header position-relative">
          <div class="header-title">
            <h1><?= $activeOpt["opt"]; ?></h1>
          </div>
          <div class="header-buttons">
            <a class="sidebar-toggler" href="#"><i class="fa fa-arrows-h"></i></a>
            <a class="refresh" id="refresh-toggler" href=""><i class="glyphicon glyphicon-refresh"></i></a>
            <a class="fullscreen" id="fullscreen-toggler" href="#"><i class="glyphicon glyphicon-fullscreen"></i></a>
          </div>
        </div>
        <div class="page-body">
          <?php include_once($activeOpt["url"]); ?>
        </div>
      </div>
    </div>
  </div>
</body>
<script src="<?= CFG::Root(); ?>assets/js/beyond.min.js"></script>
</html>

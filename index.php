<?php
@require_once("app/rules/functions.php");
Session::Clear();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" ng-app="angular-app">
<head>
  <meta charset="utf-8" />
  <title>Ministérios IASD - Conecte-se</title>
  <meta name="description" content="Dashboard" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="shortcut icon" href="<?= CFG::Root(); ?>img/favico.png" type="image/x-icon">
  <link href="<?= CFG::Root(); ?>assets/css/bootstrap.min.css" rel="stylesheet" />
  <link id="bootstrap-rtl-link" href="" rel="stylesheet" />
  <link href="<?= CFG::Root(); ?>assets/css/font-awesome.min.css" rel="stylesheet" />
  <link href="<?= CFG::Root(); ?>assets/css/weather-icons.min.css" rel="stylesheet" />
  <link href="http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,400,600,700,300" rel="stylesheet" type="text/css">
  <link href="<?= CFG::Root(); ?>assets/css/beyond.min.css" rel="stylesheet"/>
  <link href="<?= CFG::Root(); ?>assets/css/demo.min.css" rel="stylesheet" />
  <link href="<?= CFG::Root(); ?>assets/css/typicons.min.css" rel="stylesheet" />
  <link href="<?= CFG::Root(); ?>assets/css/animate.min.css" rel="stylesheet" />
  <link href="<?= CFG::Root(); ?>assets/css/bootstrap-select.min.css" rel="stylesheet" />
  <link id="skin-link" href="" rel="stylesheet" type="text/css" />
  <script src="<?= CFG::Root(); ?>assets/js/skins.min.js"></script>
</head>
<body>
  <div class="login-container animated fadeInDown">
    <div class="loginbox bg-white">
      <div class="loginbox-title">Conecte-se</div>
      <?php
      /*
            <div class="loginbox-social">f
                <div class="social-title ">Entrar utilizando sua rede social</div>
                <div class="social-buttons">
                    <a href="" class="button-facebook">
                        <i class="social-icon fa fa-facebook"></i>
                    </a>
                    <a href="" class="button-twitter">
                        <i class="social-icon fa fa-twitter"></i>
                    </a>
                    <a href="" class="button-google">
                        <i class="social-icon fa fa-google-plus"></i>
                    </a>
                </div>
            </div>
            <div class="loginbox-or">
                <div class="or-line"></div>
                <div class="or">OU</div>
            </div>
			*/
      ?>
      <form lass="form-signin" method="post" id="login-form"
        data-bv-message="Conteúdo inválido"
        data-bv-feedbackicons-valid="glyphicon glyphicon-ok"
        data-bv-feedbackicons-invalid="glyphicon glyphicon-remove"
        data-bv-feedbackicons-validating="glyphicon glyphicon-refresh">
        <div class="form-group">
          <div class="col-lg-12 loginbox-textbox">
            <span class="input-icon">
              <i class="fa fa-envelope green"></i>
              <input class="form-control" name="email" id="email" type="email" placeholder="Email"
                data-bv-emailaddress="true"
                data-bv-emailaddress-message="Email inválido" />
            </span>
          </div>
        </div>
        <div class="form-group">
          <div class="col-lg-12 loginbox-textbox">
            <span class="input-icon">
              <i class="fa fa-lock green"></i>
              <input type="password" class="form-control" name="psw" id="psw" placeholder="Senha"
                data-bv-notempty="true"
                data-bv-notempty-message="Senha obrigatória" />
            </span>
          </div>
        </div>
        <div class="loginbox-submit">
          <button type="submit" class="btn btn-palegreen btn-block"><span class="glyphicon glyphicon-off"></span>&nbsp;Entrar</button>
        </div>
        <div class="loginbox-signup">
          <a href="define.php">Esqueci</a> ou <a href="define.php">não tenho</a> a senha.<br />
          Acesso <a href="define.php">bloqueado</a>?<br />
          Ainda não registrado? <a href="register.php">Registre-se</a>!<br />
        </div>
      </form>
    </div>
    <div class="logobox">
      <center><img src="<?= CFG::Root(); ?>img/logo.png" width="80px" height="43px" /></center>
    </div>
  </div>
  <script src="<?= CFG::Root(); ?>assets/js/jquery.min.js"></script>
  <script src="<?= CFG::Root(); ?>assets/js/angular.min.js"></script>
  <script src="<?= CFG::Root(); ?>assets/js/jquery.sha1.js"></script>
  <script src="<?= CFG::Root(); ?>assets/js/bootstrap.min.js"></script>
  <script src="<?= CFG::Root(); ?>assets/js/bootstrap-dialog.min.js"></script>
  <script src="<?= CFG::Root(); ?>assets/js/bootstrap-select.min.js"></script>
  <script src="<?= CFG::Root(); ?>assets/js/slimscroll/jquery.slimscroll.min.js"></script>
  <script src="<?= CFG::Root(); ?>assets/js/beyond.min.js"></script>
  <script src="<?= CFG::Root(); ?>assets/js/validation/bootstrapValidator.min.js"></script>
  <script src="<?= CFG::Root(); ?>app/js/functions.lib.js?<?= microtime(); ?>"></script>
  <script src="<?= CFG::Root(); ?>index.js?<?= microtime(); ?>"></script>
  <script>jsLIB.rootDir = '<?= CFG::Root(); ?>';</script>
</body>
</html>

<?php
@require_once("../../rules/functions.php");
Session::Start();
responseMethod();

/****************************
 * Methods defined for use. *
 ****************************/
function login($parameters)
{
  $usr = strtolower($parameters["username"]);
  $psw = strtolower($parameters["password"]);

  $arr = array();
  $arr['page'] = "";
  $arr['login'] = false;
  $arr['message'] = "Login inválido!";

  //Verificacao de Usuario/Senha
  if (isset($usr) && !empty($usr)):

    $result = CONN::get()->Execute("SELECT * FROM CD_PERSON WHERE email = ?", array($usr));
    if (!$result->EOF):
      $ativo = $result->fields['is_active'];

      //CADASTRO INATIVO
      if ($ativo == 'N'):
        $valido = $result->fields['cd_valid'];

        //FALTA DE VALIDACAO DO EMAIL
        if (isset($valido) && !empty($valido)):
          $arr['page'] = CFG::Root() . "register.php?hint=$usr";
          $arr['login'] = true;

        //RECUPERACAO DE CADASTRO BLOQUEADO
        else:
          $arr['page'] = CFG::Root() . "define.php?hint=$usr";
          $arr['login'] = true;
        endif;

      elseif ($ativo == 'S'):
        $idBD = $result->fields['id'];
        $tentBD = $result->fields['tent'] + 1;
        $senhaBD = $result->fields['pass'];

        //PESSOA JA CADASTRADA, BASTA PEDIR NOVA SENHA
        if (is_null($senhaBD) || empty($senhaBD)):
          $arr['page'] = CFG::Root() . "define.php?hint=$usr";
          $arr['login'] = true;

        //ENTRAR NO DASHBOARD
        elseif ($senhaBD == $psw):
          CONN::get()->Execute("UPDATE CD_PERSON SET tent = 0 WHERE id = ?", array($idBD));
          Testes::VerificaTestes($idBD);
          Profile::SetSessionLogin($result);
          $arr['page'] = CFG::Root() . "app/view/dashboard.php";
          $arr['login'] = true;

        //SE ERROU A SENHA MAIS DO QUE 3 VEZES
        elseif ($tentBD > 3):
          CONN::get()->Execute("UPDATE CD_PERSON SET is_active = 'N' WHERE id = ?", array($idBD));
          $arr['message'] = "Excedido numero de tentativas.<br/>Seu usuário foi bloqueado!";

        //SE ERROU A SENHA
        else:
          CONN::get()->Execute("UPDATE CD_PERSON SET tent = ? WHERE id = ?", array($tentBD, $idBD));

        endif;

      endif;

    endif;
  endif;
  return $arr;
}

function logout()
{
  session_start();
  session_destroy();
  return array('logout' => true);
}

function register($parameters)
{
  $usr = strtolower($parameters["username"]);
  $psw = strtolower($parameters["password"]);
  $cnf = strtolower($parameters["confirm"]);
  $nam = strtoupper($parameters["name"]);

  $arr = array();
  $arr['page'] = "";
  $arr['message'] = "Confira seus dados e tente novamente.";
  $arr['register'] = false;

  //Verificacao de Usuario/Senha
  if (isset($usr) && !empty($usr)):
    if ($psw != $cnf):
      $arr['message'] = "Senha e confirmaçãoo da senha são diferentes.";
    else:

      $result = CONN::get()->Execute("SELECT * FROM CD_PERSON WHERE email = ?", array($usr));

      $protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
      $host = $_SERVER['HTTP_HOST'] . CFG::Root();

      //SE EXISTE CADASTRO
      if (!$result->EOF):
        $ativo = $result->fields['is_active'];
        $idBD = $result->fields['id'];
        $nam = $result->fields['nm'];
        $senhaBD = $result->fields['pass'];

        //CADASTRO INATIVO
        if ($ativo == 'N'):
          $arr = fSetRecover($result, $arr);

        //CADASTRO ATIVO
        elseif ($ativo == 'S'):

          //NAO TEM SENHA NO BANCO
          if (!empty($psw) && (is_null($senhaBD) || empty($senhaBD))):
            CONN::get()->Execute("UPDATE CD_PERSON SET pass = ?, tent = 0 WHERE id = ?", array($psw, $idBD));

            Profile::VerificaPerfil($idBD);
            Testes::VerificaTestes($idBD);
            Profile::SetSessionLogin($result);
            $arr['page'] = "$protocolo://$host" . "app/view/dashboard.php";
            $arr['register'] = true;

          //TEM SENHA NO BANCO, MAS NAO LEMBRA A SENHA
          elseif (empty($psw)):
            CONN::get()->Execute("UPDATE CD_PERSON SET pass = NULL, is_active = 'N' WHERE id = ?", array($idBD));
            $arr = fSetRecover($result, $arr);

          endif;

        endif;

      //SE CADASTRO NAO EXISTE
      else:
        $valido = md5($usr . $psw . strtotime("now"));
        $mail = Mail::Get();
        $mail->ClearAllRecipients();
        $mail->AddAddress($usr, $nam);
        $mail->Subject = "Bem vindo ao site pexsys.info/dons";
        $linkValid = "$protocolo://$host" . "register.php?id=$valido";
        $mail->MsgHTML(str_replace(array("&lt;", "&gt;"), array("<", ">"), htmlentities("
        Caro(a) usuário(a),<br/>
        <br/>
        Esta mensagem refere-se a solicitação de registro no site de dons do pexsys.info.<br/>
        <br/>
        Desde já, agradecemos seu registro e esperamos que nosso site lhe ajude a encontrar seus dons, e que estes possam lhe guiar ao seu ministério, e sendo assim, que seu ministério possa ajudá-lo a ser feliz trabalhando para Deus.<br/>
        <br/>
        Para confirmar seu cadastro, acesse o endereço abaixo:<br/>
        <a href=\"$linkValid\">$linkValid</a><br/>
        <br/>
        Um grande abraço<br/>
        <br/>
        pexsys.info/dons<br/>
        ", ENT_NOQUOTES, 'UTF-8', false)));

        //Mensagem
        if ($mail->Send()):
          CONN::get()->Execute("INSERT INTO CD_PERSON (email, pass, nm, cd_valid, is_active) VALUES(?,?,?,?,?)", array($usr, $psw, $nam, $valido, 'N'));
          $arr['page'] = "$protocolo://$host" . "register.php?hint=$usr";
          $arr['register'] = true;

        else:
          $arr['message'] = "Não foi possível validar seu email. Tente novamente mais tarde.";
          $arr['register'] = false;

        endif;

      endif;
    endif;

  endif;
  return $arr;
}

function fSetRecover($result, $ret)
{
  $valido = md5($result->fields["id"] . $result->fields["pass"] . strtotime("now"));
  $protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
  $host = $_SERVER['HTTP_HOST'] . CFG::Root();

  $mail = Mail::Get();
  $mail->ClearAllRecipients();
  $mail->AddAddress($result->fields["email"], $result->fields["nm"]);
  $mail->Subject = "Bem vindo ao site pexsys.info/dons";
  $linkValid = "$protocolo://$host" . "define.php?id=$valido";
  $mail->MsgHTML(str_replace(array("&lt;", "&gt;"), array("<", ">"), htmlentities("
  Caro(a) usuário(a),<br/>
  <br/>
  Esta mensagem refere-se a solicitação de recuperação e/ou ativação de sua conta no site pexsys.info/dons.<br/>
  <br/>
  Desde já, agradecemos seu interesse em continuar conosco e esperamos que nosso site continue a lhe ajudar em sua caminhada cristã com seus dons e ministérios.<br/>
  <br/>
  Para confirmar seu pedido, acesse o endereço abaixo:<br/>
  <a href=\"$linkValid\">$linkValid</a><br/>
  <br/>
  Um grande abraço,<br/>
  pexsys.info/dons<br/>
  ", ENT_NOQUOTES, 'UTF-8', false)));

  if ($mail->Send()):
    CONN::get()->Execute("UPDATE CD_PERSON SET cd_valid = ? WHERE id = ?", array($valido, $result->fields["id"]));
    $ret['page'] = "$protocolo://$host" . "register.php?hint=" . $result->fields["email"];
    $ret['register'] = true;

  else:
    $ret['message'] = "Não foi possível validar seu email. Tente novamente mais tarde.";
    $ret['register'] = false;

  endif;
  return $ret;
}

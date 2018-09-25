<?php

class AuthorizationAttribute
{
  public $attributes;

  public function run($context)
  {
    if (in_array("header", $this->attributes)) {
      if (in_array("Authorization", $this->attributes)) {
        $response = new Response();
        $response->setContentType("application/json");
        $response->addHeader("Pragma: no-cache");

        if ($context->request->authorization['DT_EXP'] < Formatter::DateToday()) {
          $response->setStatusCode(403);
          return $response;
        }

        $data = JWT::decode($context->request->authorization['TOKEN'], $context->request->authorization['SECRET']);
        if ($data["iss"] !== $_SERVER["SERVER_NAME"]) {
          $response->setStatusCode(403);
          return $response;
        } elseif ($data["env"] !== CFG::Get()->Var('Env')) {
          $response->setStatusCode(403);
          return $response;
        }

        $context->request->authorization['data'] = $data;
      }

      //Autenticado
    } elseif (Session::KeyExists('USER')) {
      // if (in_array("header_authorization", $this->attributes)) {
      //   $repository = new Repository();
      //   $result = $repository->getAuthorizationToken($context->request->route);
      //   if ($result == false) {
      //     $response = new Response();
      //     $response->setContentType("application/json");
      //     $response->addHeader("Pragma: no-cache");
      //     $response->setStatusCode(403);
      //     $response->setContent(array("erro" => "Acesso negado"));
      //     return $response;
      //   }

      //   $dateAtu = Formatter::dateTimeModify();
      //   if (isset($result->dh_fim_acesso)) {
      //     $intervalo = $dateAtu->diff(new DateTime($result->dh_fim_acesso, $GLOBALS['TIME_ZONE']), false);
      //     $dias = ($intervalo->invert == 1) ? ($intervalo->days * -1) : $intervalo->days;
      //     if ($result->dia_vct_mensal == 99 || $dias <= 0) {
      //       $response = new Response();
      //       $response->setContentType("application/json");
      //       $response->addHeader("Pragma: no-cache");
      //       $response->setStatusCode(403);
      //       $response->setContent(array("erro" => "Acesso negado"));
      //       return $response;
      //     }
      //   } elseif (isset($result->qtd_by_minute) && !is_null($result->qtd_by_minute)) {
      //     if (!isset($result->dh_last) || is_null($result->dh_last) || $dateAtu->format('Y-m-d H:i:s') > $result->dh_last) {
      //       $newExpire = $dateAtu->modify("+1 minute")->format('Y-m-d H:i:s');
      //       $repository->updateApiTokenExecution($result->id, $newExpire, 1);
      //     } else if ($result->count_by_minute >= $result->qtd_by_minute) {
      //       $response = new Response();
      //       $response->setContentType("application/json");
      //       $response->addHeader("Pragma: no-cache");
      //       $response->setStatusCode(429);
      //       $response->setContent(array("erro" => "Tentativas excedidas"));
      //       return $response;
      //     } else {
      //       $repository->incrementApiTokenExecution($result->id);
      //     }
      //   }
      // } else {
      //   if (in_array(Session::User('perfil'), $this->attributes) == false) {
      //     $response = new Response();
      //     $response->setStatusCode(403);
      //     return $response;
      //   }

      //   $repository = new Repository();
      //   if (!$repository->checkSession() || !Session::KeyExists('USER', 'ativo') || !Session::KeyExists('USER', 'status_aprovacao')) {
      //     $context->redirect('logout/');
      //     return;
      //   } elseif (Session::User('ativo') != 1) {
      //     if (!in_array("aguardando", $this->attributes)) {
      //       if (Session::User('status_aprovacao') == 'Aguardando aprovação') {
      //         $context->redirect('aguardando/');
      //         return;
      //       } else if (Session::User('status_aprovacao') == 'Aguardando contratação') {
      //         $context->redirect('aguardando-contrato/');
      //         return;
      //       }
      //     } else {
      //       return;
      //     }
      //     $context->redirect('logout/');
      //     return;
      //   }

      //   if (!in_array("meuperfil", $this->attributes)) {
      //     $incompleto = $repository->cadastroIncompleto(Session::User('id'));
      //     if (isset($incompleto)) {
      //       Session::User('notificacao', "<div class='fadein alert alert-warning' role='alert'>Antes de acessar as funcionalidades, você precisa completar seu cadastro!</div>");
      //       Session::User('completar', true);
      //       $context->redirect('meuperfil/');
      //       return;
      //     }

      //     if (!in_array("degustacao", $this->attributes)) {
      //       $verificaDegustacao = $repository->verificaDegustacao();
      //       if (!isset($verificaDegustacao)) {
      //         $context->redirect('degustacao/');
      //         return;
      //       } elseif ($verificaDegustacao == 44) {
      //         $context->redirect('degustacao-4/');
      //         return;
      //       } elseif ($verificaDegustacao == 99) {
      //         $context->redirect('acesso-expirado/');
      //         return;
      //       } elseif ($verificaDegustacao > 44 && $verificaDegustacao != 999) {
      //         $context->redirect('degustacao-fim/');
      //         return;
      //       }
      //     }
      //   }
      // }
    }
  }
}

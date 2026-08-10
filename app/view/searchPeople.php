<script src="<?= CFG::Root(); ?>assets/js/datatable/jquery.dataTables.min.js"></script>
<script src="<?= CFG::Root(); ?>assets/js/datatable/ZeroClipboard.js"></script>
<script src="<?= CFG::Root(); ?>assets/js/datatable/dataTables.bootstrap.min.js"></script>
<script src="<?= CFG::Root(); ?>assets/js/jquery-progress-bar.js"></script>
<?php @require_once("rules/testes.php"); ?>
<div class="col-xs-12 col-md-12" id="divTest" style="display:none">
  <div class="row">
    <div class="text-center">
      <a id="btnBack" href="javascript:void(0);" class="btn btn-labeled btn-darkorange pull-left">
        <i class="btn-label glyphicon glyphicon-user"></i>Voltar
      </a>
      <a id="btnFinishTest" href="javascript:void(0);" class="btn btn-labeled btn-success pull-right">
        <i class="btn-label glyphicon glyphicon-floppy-saved"></i>Finalizar Teste
      </a>
    </div>
  </div>
  <br />
  <div class="row">
    <div class="widget">
      <div class="widget-header bg-sky" name="divHead" id="divGiftTest">
        <span class="widget-caption">Para cada quadro abaixo, anote o numero da coluna correspondente ao teste:</span>
        <div class="widget-buttons">
          <div class="progress progress-striped active" id="myProgressbar" style="width:300px">
            <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
              <span>0% completado</span>
            </div>
          </div>
        </div>
      </div>
      <div class="widget-header bg-sky" name="divHead" id="divMiniTest">
        <span class="widget-caption">Para cada ministério escolhido na lista, dê a nota de 1 a 10.</span>
      </div>
      <div class="widget-body">
        <div class="row">
          <div class="col-xs-12 col-md-12" id="divTestBody" id-pessoa=""></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="divGridSearch" style="display:block">
  <div class="row">
    <div class="col-xs-12 col-md-12">
      <?php fDataFilters(
        array(
          "filterTo" => "#peopleDatatable",
          "filters" =>
          array(
            array("value" => "D", "label" => "Dom"),
            array("value" => "DI", "label" => "Pontua&ccedil;&atilde;o Dons igual"),
            array("value" => "DA", "label" => "Pontua&ccedil;&atilde;o Dons maior", "unique" => true),
            array("value" => "DE", "label" => "Pontua&ccedil;&atilde;o Dons menor", "unique" => true),

            array("value" => "M", "label" => "Minist&eacute;rio"),
            array("value" => "MI", "label" => "Nota Minist&eacute;rio igual"),
            array("value" => "MA", "label" => "Nota Minist&eacute;rio maior", "unique" => true),
            array("value" => "ME", "label" => "Nota Minist&eacute;rio menor", "unique" => true)
          )
        )
      ); ?>
    </div>
    <div class="col-xs-12 col-md-12">
      <div class="widget">
        <div class="widget-header bordered-bottom bordered-yellow">
          <div class="widget-buttons">
            <a href="#" data-toggle="maximize">
              <i class="fa fa-expand"></i>
            </a>
            <a href="#" data-toggle="collapse">
              <i class="fa fa-minus"></i>
            </a>
            <a href="#" data-toggle="dispose">
              <i class="fa fa-times"></i>
            </a>
          </div>
        </div>
        <div class="widget-body">
          <table class="table table-bordered table-hover table-striped dataTable" id="peopleDatatable" role="grid">
            <thead class="bordered-darkorange">
              <tr role="row">
                <th></th>
                <th>Nome</th>
                <th>Email</th>
                <th>Dons</th>
                <th>Minist&eacute;rios</th>
              </tr>
            </thead>
            <tbody />
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-12 col-md-12">
      <a role="button" class="btn btn-success" id="btnNovo" style="display:none"><i class="fa fa-plus"></i>&nbsp;Nova Pessoa</a>
    </div>
  </div>
</div>

<div class="modal fade" id="membrosModal" role="dialog" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-body">
      <div class="widget radius-bordered">
        <div class="widget-header bg-lightred">
          <span class="widget-caption">Cadastro de Pessoa / Usu&aacute;rio</span>
          <span class="widget-caption pull-right" data-dismiss="modal" class="close" type="button" id="btnX">&times;&nbsp;&nbsp;</span>
        </div>
        <div class="widget-body">
          <div id="registration-form">
            <form method="post" id="cadMembrosForm">
              <input type="hidden" name="" id="membroID" field="cd_pessoa-id" />
              <div class="form-group">
                <span class="input-icon icon-right">
                  <input type="text" name="nmCompleto" id="nmCompleto" field="cd_pessoa-nm" class="form-control input-sm" placeholder="Nome Completo" style="text-transform:uppercase" />
                  <i class="glyphicon glyphicon-user circular"></i>
                </span>
              </div>
              <div class="form-group">
                <span class="input-icon icon-right">
                  <input type="text" name="dsEmail" id="dsEmail" field="cd_pessoa-email" class="form-control input-sm" placeholder="E-mail" style="text-transform:lowercase">
                  <i class="fa fa-envelope-o circular"></i>
                </span>
              </div>
            </form>
          </div>
          <div class="row">
            <div class="col-xs-12 col-md-12">
              <a href="#" id="btnDonsDlg" class="btn btn-warning btn-xs" style="display:none"><i class="fa fa-plus"></i> Adicionar Teste de Dons</a>
              <a href="#" id="btnMiniDlg" class="btn btn-warning btn-xs" style="display:none"><i class="fa fa-plus"></i> Adicionar Teste de Minist&eacute;rios</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="<?= CFG::Root(); ?>js/searchPeople.js<?php echo "?" . microtime(); ?>"></script>

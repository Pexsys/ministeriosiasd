<script src="<?= CFG::Root(); ?>assets/js/datatable/jquery.dataTables.min.js"></script>
<script src="<?= CFG::Root(); ?>assets/js/datatable/ZeroClipboard.js"></script>
<script src="<?= CFG::Root(); ?>assets/js/datatable/dataTables.bootstrap.min.js"></script>
<?php
$testes = Testes::VerificaTestes($_SESSION['PESSOA']['id']);

//SE EXISTE TESTE DE MINISTERIOS PENDENTE
if (isset($testes["minis"]) && $testes["minis"]["nr_rsp"] > 0):
?>
  <div class="col-xs-12 col-md-12" id="divGridMinisterios">
    <div class="row">
      <a id="btnFinishMinisterios" href="javascript:void(0);" class="btn btn-labeled btn-palegreen">
        <i class="btn-label glyphicon glyphicon-floppy-saved"></i>Finalizar e arquivar meu Teste de Ministérios
      </a>
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
          <table class="table table-bordered table-hover table-striped dataTable" id="simpledatatable" role="grid">
            <thead class="bordered-darkorange">
              <tr role="row">
                <th>Área</th>
                <th>Código</th>
                <th>Apenas para o(s) ministério(s) de seu interesse conforme as áreas abaixo, dê a nota de 1 a 10.</th>
              </tr>
            </thead>
            <tbody />
          </table>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-xs-12 col-md-12">
        <a role="button" class="btn btn-success" id="btnNovo" style="display:none"><i class="fa fa-plus"></i>&nbsp;Nova Pessoa</a>
      </div>
    </div>
  </div>
<?php
endif;

$ultimoResultado = false;
//EXIBE RESULTADOS
foreach (Testes::ExistHistorico($_SESSION['PESSOA']['id'], 'M') as $result):
?>
  <div class="col-xs-12 col-md-12">
    <div class="row">
      <div class="well with-header">
        <div class="header bg-blue">
          <?php
          if (!$ultimoResultado):
            $ultimoResultado = true;
            echo "<span class=\"btn btn-primary\">ÚLTIMO RESULTADO</span>&nbsp;";
          endif;
          ?>
          Concluído em: <?= Conversion::StrToDate($result['dh_conclusion'], "d/m/Y"); ?>
          <span class="pull-right" style="cursor:pointer" name="printResult" id-teste="<?= $result['id']; ?>"><i class="fa fa-search-plus fa-2x"></i></span>
        </div>
        <table class="table table-hover">
          <thead class="bordered-darkorange">
            <tr>
              <th>Ordem</th>
              <th>Ministério</th>
              <th>Nota</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $ordem = 0;
            foreach (Testes::QueryResult($result['id']) as $rsitem):
            ?>
              <tr>
                <td><?= ++$ordem; ?>&ordm;</td>
                <td><?= $rsitem['ds']; ?></td>
                <td><?= Testes::LegendaDisposicao($rsitem['seq']); ?></td>
              </tr>
            <?php
            endforeach;
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php
endforeach;
?>
<script src="<?= CFG::Root(); ?>app/js/dashboard_testeministerios.js<?= "?" . microtime(); ?>"></script>

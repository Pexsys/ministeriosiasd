<script src="<?= CFG::Root(); ?>assets/js/datatable/jquery.dataTables.min.js"></script>
<script src="<?= CFG::Root(); ?>assets/js/datatable/ZeroClipboard.js"></script>
<script src="<?= CFG::Root(); ?>assets/js/datatable/dataTables.tableTools.min.js"></script>
<script src="<?= CFG::Root(); ?>assets/js/datatable/dataTables.bootstrap.min.js"></script>
<script src="<?= CFG::Root(); ?>assets/js/jquery-progress-bar.js"></script>
<?php
$testes = Testes::VerificaTestes($_SESSION['PESSOA']['id']);

//SE EXISTE TESTE DE DONS PENDENTE
if (isset($testes["dons"]) && $testes["dons"]["nr_rsp"] > 0):
?>
  <div class="row">
    <div class="col-xs-12 col-md-12 text-center" style="margin-bottom:10px">
      <a id="btnFinishDons" href="javascript:void(0);" class="btn btn-labeled btn-palegreen">
        <i class="btn-label glyphicon glyphicon-floppy-saved"></i>Finalizar e mostrar meu resultado do Teste de Dons
      </a>
    </div>
    <div class="col-xs-12 col-md-12 text-center">
      <div id="myProgressbar" class="progress progress-striped active">
        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
          <span>0% completado</span>
        </div>
      </div>
    </div>
    <div class="col-xs-12 col-md-12">
      <div class="widget">
        <div class="widget-header bordered-bottom bordered-yellow">
          <span class="widget-caption">Responda as questões em todas as páginas abaixo:</span>
        </div>
        <div class="widget-body">
          <table class="table table-condensed table-hover compact cell-border" id="simpledatatable">
            <thead class="bordered-darkorange">
              <tr>
                <th>Selecione a resposta que melhor se encaixa a você para cada questão abaixo:</th>
              </tr>
            </thead>
            <tbody />
          </table>
        </div>
      </div>
    </div>
  </div>
<?php
endif;

$ultimoResultado = false;
//EXIBE RESULTADOS
foreach (Testes::ExistHistorico($_SESSION['PESSOA']['id'], 'D') as $result):
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
          Concluído em <?= Conversion::StrToDate($result['dh_conclusion'], "d/m/Y"); ?>
          <span class="pull-right" style="cursor:pointer" name="printResult" id-teste="<?= $result['id']; ?>"><i class="fa fa-search-plus fa-2x"></i></span>
        </div>
        <table class="table table-hover">
          <thead class="bordered-darkorange">
            <tr>
              <th>Ordem</th>
              <th>Dom</th>
              <th>Pontuação</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $ordem = 0;
            foreach (Testes::QueryResult($result['id']) as $rsitem):
            ?>
              <tr name="detalheDom" id-ref="<?= $rsitem['id_source']; ?>" style="cursor:pointer">
                <td><?= ++$ordem; ?>&ordm;</td>
                <td><?= $rsitem['ds']; ?></td>
                <td><?= $rsitem['seq']; ?></td>
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
<script src="<?= CFG::Root(); ?>app/js/dashboard_testedons.js<?= "?" . microtime(); ?>"></script>

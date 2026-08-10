<script src="<?= CFG::Root(); ?>assets/js/datatable/jquery.dataTables.min.js"></script>
<script src="<?= CFG::Root(); ?>assets/js/datatable/ZeroClipboard.js"></script>
<script src="<?= CFG::Root(); ?>assets/js/datatable/dataTables.tableTools.min.js"></script>
<script src="<?= CFG::Root(); ?>assets/js/datatable/dataTables.bootstrap.min.js"></script>
<script src="<?= CFG::Root(); ?>assets/js/jquery-progress-bar.js"></script>
<?php
@require_once("rules/testes.php");
$testes = Testes::VerificaTestes($_SESSION['PESSOA']['id']);

//SE EXISTE TESTE DE DONS PENDENTE
if ($testes["dons"]["nr_rsp"] > 0):
?>
  <div class="row">
    <div class="col-xs-12 col-md-12 text-center">
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
          <span class="widget-caption">Responda as quest&otilde;es em todas as p&aacute;ginas abaixo:</span>
        </div>
        <div class="widget-body">
          <table class="table table-condensed table-hover compact cell-border" id="simpledatatable">
            <thead class="bordered-darkorange">
              <tr>
                <th>Selecione a resposta que melhor se encaixa a voc&ecirc; para cada quest&atilde;o abaixo:</th>
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
            echo "<span class=\"btn btn-primary\">&Uacute;LTIMO RESULTADO</span>&nbsp;";
          endif;
          ?>
          Conclu&iacute;do&nbsp;em:&nbsp;<?php echo strftime("%d/%m/%Y", strtotime($result['dh_conclusion'])); ?>
          <span class="pull-right" style="cursor:pointer" name="printResult" id-teste="<?php echo $result['id']; ?>"><i class="fa fa-search-plus fa-2x"></i></span>
        </div>
        <table class="table table-hover">
          <thead class="bordered-darkorange">
            <tr>
              <th>Ordem</th>
              <th>Dom</th>
              <th>Pontua&ccedil;&atilde;o</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $ordem = 0;
            foreach (fQueryResult($result['id']) as $rsitem):
            ?>
              <tr name="detalheDom" id-ref="<?php echo $rsitem['id_source']; ?>" style="cursor:pointer">
                <td><?php echo ++$ordem; ?>&ordm;</td>
                <td><?php echo utf8_encode($rsitem['ds']); ?></td>
                <td><?php echo $rsitem['seq']; ?></td>
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
<script src="<?= CFG::Root(); ?>js/dashboard_testedons.js<?php echo "?" . microtime(); ?>"></script>

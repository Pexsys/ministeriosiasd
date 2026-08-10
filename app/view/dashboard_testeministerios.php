<script src="<?= CFG::Root(); ?>assets/js/datatable/jquery.dataTables.min.js"></script>
<script src="<?= CFG::Root(); ?>assets/js/datatable/ZeroClipboard.js"></script>
<script src="<?= CFG::Root(); ?>assets/js/datatable/dataTables.bootstrap.min.js"></script>
<?php
@require_once("rules/testes.php");
$testes = Testes::VerificaTestes($_SESSION['PESSOA']['id']);

//SE EXISTE TESTE DE MINISTERIOS PENDENTE
if ($testes["minis"]["nr_rsp"] > 0):
?>
  <div class="col-xs-12 col-md-12" id="divGridMinisterios">
    <div class="row">
      <a id="btnFinishMinisterios" href="javascript:void(0);" class="btn btn-labeled btn-palegreen">
        <i class="btn-label glyphicon glyphicon-floppy-saved"></i>Finalizar e arquivar meu Teste de Minist&eacute;rios
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
                <th>&Aacute;rea</th>
                <th>C&oacute;digo</th>
                <th>Apenas para o(s) minist&eacute;rio(s) de seu interesse conforme as &aacute;reas abaixo, dê a nota de 1 a 10.</th>
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
              <th>Minist&eacute;rio</th>
              <th>Nota</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $ordem = 0;
            foreach (fQueryResult($result['id']) as $rsitem):
            ?>
              <tr>
                <td><?php echo ++$ordem; ?>&ordm;</td>
                <td><?php echo utf8_encode($rsitem['ds']); ?></td>
                <td><?php echo Testes::LegendaDisposicao($rsitem['seq']); ?></td>
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
<script src="<?= CFG::Root(); ?>js/dashboard_testeministerios.js<?php echo "?" . microtime(); ?>"></script>

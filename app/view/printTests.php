<script src="<?= CFG::Root(); ?>assets/js/charts/sparkline/jquery.sparkline.js"></script>
<script src="<?= CFG::Root(); ?>assets/js/charts/sparkline/sparkline-init.js"></script>
<script src="<?= CFG::Root(); ?>assets/js/charts/easypiechart/jquery.easypiechart.min.js"></script>
<script src="<?= CFG::Root(); ?>assets/js/charts/easypiechart/easypiechart-init.js"></script>
<script src="<?= CFG::Root(); ?>assets/js/charts/flot/jquery.flot.min.js"></script>
<script src="<?= CFG::Root(); ?>assets/js/charts/flot/jquery.flot.resize.min.js"></script>
<script src="<?= CFG::Root(); ?>assets/js/charts/flot/jquery.flot.pie.min.js"></script>
<script src="<?= CFG::Root(); ?>assets/js/charts/flot/jquery.flot.tooltip.min.js"></script>
<script src="<?= CFG::Root(); ?>assets/js/charts/flot/jquery.flot.orderBars.js"></script>
<div class="row">
  <div id="divDons" class="col-lg-2 col-sm-4 col-xs-6" style="cursor:pointer">
    <div class="databox databox-lg databox-vertical databox-inverted bg-white databox-shadowed">
      <div class="databox-top">
        <div class="databox-piechart">
          <div id="divColor" data-toggle="easypiechart" class="easyPieChart block-center" data-barcolor="#e75b8d" data-linecap="butt" data-percent="0" data-animate="500" data-linewidth="8" data-size="100" data-trackcolor="#eee" style="width: 100px; height: 100px; line-height: 100px;">
            <span class="white font-200"><i class="fa fa-download primary"></i></span>
            <canvas width="200" height="200" style="width: 100px; height: 100px;"></canvas>
          </div>
        </div>
      </div>
      <div class="databox-bottom no-padding text-align-center">
        <span class="databox-number lightcarbon no-margin">Teste de Dons</span>
        <span class="databox-text lightcarbon no-margin" id="divData">0 downloads</span>
      </div>
    </div>
  </div>
  <div id="divMini" class="col-lg-2 col-sm-4 col-xs-6" style="cursor:pointer">
    <div class="databox databox-lg databox-vertical databox-inverted bg-white databox-shadowed">
      <div class="databox-top">
        <div class="databox-piechart">
          <div id="divColor" data-toggle="easypiechart" class="easyPieChart block-center" data-barcolor="#e75b8d" data-linecap="butt" data-percent="0" data-animate="500" data-linewidth="8" data-size="100" data-trackcolor="#eee" style="width: 100px; height: 100px; line-height: 100px;">
            <span class="white font-200"><i class="fa fa-download primary"></i></span>
            <canvas width="200" height="200" style="width: 100px; height: 100px;"></canvas>
          </div>
        </div>
      </div>
      <div class="databox-bottom no-padding text-align-center">
        <span class="databox-number lightcarbon no-margin">Teste de Ministérios</span>
        <span class="databox-text lightcarbon no-margin" id="divData">0 downloads</span>
      </div>
    </div>
  </div>
</div>
<script src="<?= CFG::Root(); ?>app/js/printTests.js<?php echo "?" . microtime(); ?>"></script>

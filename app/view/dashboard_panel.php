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
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="row" ng-controller="dashboard">
      <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12" ng-repeat="panel in panels">
        <div class="databox bg-white radius-bordered">
          <div class="databox-left {{panel.leftBkTheme}}">
            <div class="databox-piechart">
              <div data-toggle="easypiechart" class="easyPieChart" data-barcolor="#fff" data-linecap="butt" data-percent="{{panel.pc}}" data-animate="500" data-linewidth="3" data-size="47" data-trackcolor="rgba(255,255,255,0.1)"><span class="white font-90">{{panel.pc}}%</span></div>
            </div>
          </div>
          <div class="databox-right">
            <span class="{{panel.rightBkTheme}}">{{panel.qt}}</span>
            <div class="databox-text darkgray">{{panel.ds}}</div>
            <div class="{{panel.rightDecorate}}">
              <i class="{{panel.ico}}"></i>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<script src="<?= CFG::Root(); ?>js/dashboard_panel.js"></script>

$(document).ready(function () {
  InitiateEasyPieChart.init();
  updateGraphs();

  $("#divDons").click(function (event) {
    window.location.href = `${jsLIB.rootDir}report/printTesteDons.php?timestamp=${(new Date()).getMilliseconds()}`;
    updateGraphs('D');
  });
  $("#divMini").click(function (event) {
    window.location.href = `${jsLIB.rootDir}report/printTesteMinisterios.php?timestamp=${(new Date()).getMilliseconds()}`;
    updateGraphs('M');
  });
});

function updateGraphs(update = '') {
  return jsLIB.ajax({
    url: `${jsLIB.rootDir}app/api/dashboard/`, data: { MethodName: 'downloads', data: { update } },
    success: data => {
      const setGraph = barId => {
        const values = data.downloads[barId];
        $(`#${barId}`).find("#divData").text(`${values.qt} downloads`);
        $(`#${barId}`).find("#divColor").data('easyPieChart').options.barColor = values.cl;
        $(`#${barId}`).find("#divColor").data('easyPieChart').update(values.pc);
      }
      setGraph("divDons");
      setGraph("divMini");
    },
  });
};

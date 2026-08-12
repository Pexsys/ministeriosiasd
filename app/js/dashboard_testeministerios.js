$(document).ready(function () {

  const mapQuestao = () => {
    $("[name=questao]").change(function (e) {
      var value = $(this).val();
      jsLIB.ajax({
        url: `${jsLIB.rootDir}app/api/tests/`,
        data: { MethodName: 'setRsMinisterios', data: { id_qs: $(this).attr('id-questao'), grade: value } },
        success: function (data, jqxhr) {
          if (data.return == true) {
            e.preventDefault();
          }
        }
      });
    });
  };


  let groupColumn = 0;
  $('#simpledatatable')
    .on('init.dt', function () {
      mapQuestao();
    })
    .DataTable({
      sDom: "Tflt<'row DTTTFooter'<'col-sm-6'i><'col-sm-6'p>>",
      lengthChange: false,
      ordering: true,
      paging: false,
      scrollY: 470,
      searching: true,
      processing: true,
      language: {
        search: "",
        searchPlaceholder: "Procurar...",
        info: "_TOTAL_ sugestões de ministérios",
        loadingRecords: "Aguarde - carregando...",
        paginate: {
          first: '<<',
          previous: '<',
          next: '>',
          last: '>>'
        }
      },
      ajax: {
        type: "POST",
        url: `${jsLIB.rootDir}app/api/tests/`,
        data: () => ({ MethodName: "questoesMinisterios" }),
        dataSrc: data => data.questoes,
      },
      columns: [
        {
          data: 'da',
          sortable: false
        },
        {
          data: 'cd',
          width: "8%",
          sortable: true
        },
        {
          data: 'ds',
          width: "92%",
          sortable: false
        }
      ],
      order: [1, 'asc'],
      drawCallback: function (settings) {
        var api = this.api();
        var rows = api.rows({ page: 'current' }).nodes();
        var last = null;

        api.column(groupColumn, { page: 'current' }).data().each(function (group, i) {
          if (last !== group) {
            $(rows).eq(i).before(
              '<tr style="background-color:#707070;color:#ffffff;text-transform:uppercase"><td colspan="2">' + group + '</td></tr>'
            );
            last = group;
          }
        });
      },
      columnDefs: [
        { "visible": false, "targets": groupColumn }
      ],
      select: {
        style: 'multi',
        selector: 'td:first-child'
      }
    })
    .on('draw.dt', function () {
      mapQuestao();
    });

  $('#btnFinishMinisterios').click(function () {
    jsLIB.ajax({
      url: `${jsLIB.rootDir}app/api/tests/`,
      data: { MethodName: 'finalizarMinisterios' },
      success: data => {
        window.location.reload(true);
      }
    });
  });

});


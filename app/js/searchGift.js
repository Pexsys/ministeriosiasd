var oTable = undefined;

$(document).ready(function(){
	
	oTable = $('#giftDatatable').DataTable({
		sDom: "Tflt<'row DTTTFooter'<'col-sm-6'i><'col-sm-6'p>>",
		lengthChange: false,
		ordering: true,
		paging: false,
		scrollY: 300,
		searching: true,
		processing: true,
		language: {
			info: "_END_ pessoas",
			search: "",
			searchPlaceholder: "Procurar...",
			infoFiltered: " de _MAX_",
			loadingRecords: "Aguarde - carregando...",
			zeroRecords: "Dados indisponíveis para esta seleção",
			infoEmpty: "0 encontrados"
		},
		ajax: {
			type	: "POST",
			url	: jsLIB.rootDir+"app/api/search",
			data	: function (d) {
					d.MethodName = "getDons",
					d.data = { 
							 filtro: 'T',
							 filters: jsFilter.jSON()
						}
				},
			dataSrc: "dons"
		},
		columns: [
			{	data: "nm",
				width: "54%"
			},
			{	data: "cd",
				width: "7%"
			},
			{	data: "dm",
				width: "30%"
			},
			{	data: "nt",
				width: "9%"
			}
		],
		select: {
			style: 'multi',
			selector: 'td:first-child'
		}
	}).order( [ 2, 'desc' ], [ 0, 'asc' ], [ 1, 'asc' ] );	
});

$(window).bind("load", function () {
	InitiateEasyPieChart.init();
});

myApp.controller('dashboard', ['$scope', function ($scope) {
	var data = jsLIB.call( false, jsLIB.rootDir+"app/api/dashboard", { MethodName : 'painel' }, 'RETURN' );
	$scope.panels = data.panels
}]);

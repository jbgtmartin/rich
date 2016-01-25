'use strict';


app.controller('MainCtrl', ['$scope', '$location', function ($scope, $location) {

	$scope.isActive = function(path) {
		return $location.path() === path;
	};

}]);
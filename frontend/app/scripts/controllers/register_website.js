'use strict';

app.controller('RegisterWebsiteCtrl', ['$scope', '$http', '$location', function ($scope, $http, $location) {

	$scope.submit = function(website) {
		$scope.response = "Adding website...";
		$http({
			method: 'GET',
		  	url: base_url + 'backend2/api/websites/add/',
		  	params: {
		  		url: website.url,
		  		place: website.place,
		  		type: website.type,
		  		mail: website.email
		  	}
		}).then(function successCallback(response) {
		    $location.path("/dashboard");
		  }, function errorCallback(response) {
		    $scope.response = response;
		  });
	};

}]);

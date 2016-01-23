'use strict';

/**
 * @ngdoc function
 * @name frontendApp.controller:DashboardCtrl
 * @description
 * # DashboardCtrl
 * Controller of the frontendApp
 */
angular.module('frontendApp')
  .controller('DashboardCtrl', ['$scope', '$http', function ($scope, $http) {

    $http({
	  method: 'GET',
	  url: base_url + 'backend2/api/websites/get/'
	}).then(function successCallback(response) {
	    $scope.all_websites = response.data;
	    showItem(response.data[0]._id);
	  }, function errorCallback(response) {
	    // called asynchronously if an error occurs
	    // or server returns response with an error status.
	  });

	function showItem(id) {
		$scope.active = $scope.all_websites.filter(
		    function(data){return data._id == id}
		)[0];
		$scope.websites_list_item_class = "active";
	}

	$scope.changeWebsite = function(id) {
		showItem(id)
	};

  }]);

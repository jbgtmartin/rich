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
    $scope.tests = ['dheikezpno', 'dbezopb'];
    $scope.cake = "cakephp";
    $http({
	  method: 'GET',
	  url: base_url + 'backend/posts'
	}).then(function successCallback(response) {
	    $scope.cake = response;
	  }, function errorCallback(response) {
	    // called asynchronously if an error occurs
	    // or server returns response with an error status.
	  });

  }]);

'use strict';

/**
 * @ngdoc function
 * @name frontendApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the frontendApp
 */
angular.module('frontendApp')
	.controller('MainCtrl', ['$scope', function ($scope) {
		$scope.todos = ['Item 1', 'Item 2', 'Item 3'];

		$scope.addTodo = function() {
			$scope.todos.push($scope.todo);
			$scope.todo = '';
		};

		$scope.removeTodo = function(index) {
			$scope.todos.splice(index, 1);
		};
	}]);

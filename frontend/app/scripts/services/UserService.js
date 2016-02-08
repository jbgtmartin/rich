'use strict';


angular.module('frontendApp').factory('User', ['$http', 'localStorageService', function($http, localStorageService) {
	var login = function(email, password) {
		return $http({
			method: "POST",
			url: base_url + "backend/users/login.json",
			data: {"User": {"email": email, "password": password}}
		});
	};   

	var register = function(email, password) {
		return $http({
			method: "POST",
			url: base_url + "backend/users/add.json",
			data: {"User": {"email": email, "password": password}}
		});
	};

	var storeApiKey = function(api_key, email) {
		localStorageService.set('api_key', api_key);
		localStorageService.set('email', email);
	};

	return {
		login:login,
		register:register,
		storeApiKey: storeApiKey
	};
}]);

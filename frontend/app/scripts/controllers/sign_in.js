'use strict';


app.controller('SignInCtrl', ['$scope', 'User', function ($scope, User) {

		$scope.submit = function(user) {
			$scope.response = "Submitting...";
			User.login($scope.user.email, $scope.user.password).then(function(data) {
				var message = data.data.message;
				if(message.login_success) {
					User.storeApiKey(message.api_key, $scope.user.email);
					$scope.response = "Connecté !";
				}
				else {
					$scope.response = "Échec de la connexion, merci de réessayer.";
				}
				
			});
		};

  	}]);
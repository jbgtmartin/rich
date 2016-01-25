'use strict';


app.controller('SignUpCtrl', ['$scope', 'User', function ($scope, User) {

		$scope.submit = function(user) {
			$scope.response = "Submitting...";
			User.register($scope.user.email, $scope.user.password).then(function(data) {
				var message = data.data.message;
				if(message.register_success) {
					User.storeApiKey(message.api_key, $scope.user.email);
					$scope.response = "Compte créé avec succés.";
				}
				else {
					$scope.response = "Échec de l'inscription, merci de réessayer.";
				}
				
			});
		};

  	}]);

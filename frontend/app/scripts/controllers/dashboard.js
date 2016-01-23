'use strict';

/*
websites/add?url=compdagnieboisexotiques.fr&title=titre&text1=ligne1&text2=ligne2&place=06400&daily_budget=3450 (34€50)
websites/get/
websites/get/56a24c1cc9d9c1f0010041ab
keywords/get/56a27411c9d9c1f2010041a8
keywords/get/56a275dcc9d9c1f1010041a9/test
keywords/add/56a275dcc9d9c1f1010041a9?word=testbis&ppc=1
keywords/delete/56a275dcc9d9c1f1010041a9?word=testbis
*/

var db_websites = [
	{
		"_id" : "56a2ad1a4ee23ae23624bd74",
		"url" : "http://www.google.com/",
		"place" : "Paris",
		"type" : "architect",
		"email" : "contact@google.com",
		"pages" : [ { "url" : "http://www.google.com/fr" }, { "url" : "http://www.google.de" } ]
	},
	{ 
		"_id" : "56a2adb54ee23ae23624bd75",
		"url" : "http://www.facebook.com/",
		"place" : "Lille",
		"type" : "architect",
		"email" : "contact@facebook.com",
		"pages" : [ { "url" : "http://www.facebook.com/louisduperier" }, { "url" : "http://www.facebook.com/kevgar" } ]
	},
	{ 
		"_id" : "56a2ae244ee23ae23624bd76",
		"url" : "http://www.twitter.com/",
		"place" : "Lille",
		"type" : "teacher",
		"email" : "contact@twitter.com",
		"pages" : [ { "url" : "http://www.twitter.com/louisduperier" }, { "url" : "http://www.twitter.com/kevgar" } ]
	}
];

var db_keywords = [
	{
		"website_id" : "56a2ad1a4ee23ae23624bd74",
		"keywords" : [ { "word" : "house", "weight" : 125, "ppc" : 1.33 }, { "word" : "renovation", "weight" : 96, "ppc" : 2.72 }, { "word" : "Paris", "weight" : 94, "ppc" : 2.65 }, { "word" : "pool", "weight" : 22, "ppc" : 0.65 } ]
	},
	{
		"website_id" : "56a2adb54ee23ae23624bd75",
		"keywords" : [ { "word" : "renovation", "weight" : 118, "ppc" : 2.72 }, { "word" : "roof", "weight" : 98, "ppc" : 1.2 }, { "word" : "Lille", "weight" : 147, "ppc" : 1.41 }, { "word" : "garden", "weight" : 12, "ppc" : 1.09 }, { "word" : "teck", "weight" : 7, "ppc" : 3.9 } ]
	},
	{
		"website_id" : "56a2ae244ee23ae23624bd76",
		"keywords" : [ { "word" : "serious", "weight" : 20, "ppc" : 0.52 }, { "word" : "mathematics", "weight" : 67, "ppc" : 1.01 }, { "word" : "physics", "weight" : 89, "ppc" : 0.43 }, { "word" : "prepa", "weight" : 13, "ppc" : 1.49 }, { "word" : "PhD", "weight" : 29, "ppc" : 0.92 } ]
	}
];



/**
 * @ngdoc function
 * @name frontendApp.controller:DashboardCtrl
 * @description
 * # DashboardCtrl
 * Controller of the frontendApp
 */
app.controller('DashboardCtrl', ['$scope', '$http', '$filter', function ($scope, $http, $filter) {

 //    $http({
	// 	method: 'GET',
	//   	url: base_url + 'backend2/api/websites/get/'
	// }).then(function successCallback(response) {
	//     $scope.all_websites = response.data;
	//     displayWebsite(response.data[0]._id);
	//   }, function errorCallback(response) {
	//     // called asynchronously if an error occurs
	//     // or server returns response with an error status.
	//   });
	$scope.all_websites = db_websites;
	displayWebsite($scope.all_websites[0]._id);

	$scope.changeWebsite = function(id) {
		displayWebsite(id);
	};

	$scope.addKeyword = function() {
		var max_weight = Math.max.apply(Math,$scope.keywords.map(function(o){return o.weight;}));
		$scope.keywords.push({
			word: $scope.new_keyword,
			weight: 1.2 * max_weight
		});
		$scope.keywords = calculateNormalizedWeights($scope.keywords);
	}

	function displayWebsite(id) {
		// $http({
	 //  		method: 'GET',
	 //  		url: base_url + 'backend2/api/websites/get/' + id + '/'
		// }).then(function successCallback(response) {
		// 		response.data[0].keywords = calculateNormalizedWeights(response.data[0].keywords);
	 //    		$scope.active = response.data[0];
		//   	}, function errorCallback(response) {
		//     	// called asynchronously if an error occurs
		//     	// or server returns response with an error status.
		//   	});
		var website = $filter('filter')(db_websites, {"_id": id})[0];
		var website_keywords = $filter('filter')(db_keywords, {"website_id": id})[0];
		$scope.active = website;
		$scope.keywords = calculateNormalizedWeights(website_keywords.keywords);
	}

	function calculateNormalizedWeights(keywords) {
		var max_weight = Math.max.apply(Math,keywords.map(function(o){return o.weight;}));

		for (var i = 0; i < keywords.length ; i++){
			keywords[i].normalized_weight = keywords[i].weight / max_weight;
		}

		return keywords;
	}

}]);

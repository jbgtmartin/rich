'use strict';

/*
websites/add?url=compdagnieboisexotiques.fr&title=titre&text1=ligne1&text2=ligne2&place=06400&daily_budget=3450 (34€50)
websites/get/
websites/get/56a24c1cc9d9c1f0010041ab
keywords/get/56a27411c9d9c1f2010041a8
keywords/get/56a275dcc9d9c1f1010041a9/test
keywords/add/56a275dcc9d9c1f1010041a9?word=testbis&ppc=1
keywords/delete/56a275dcc9d9c1f1010041a9?word=testbis


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

*/

/**
 * @ngdoc function
 * @name frontendApp.controller:DashboardCtrl
 * @description
 * # DashboardCtrl
 * Controller of the frontendApp
 */
app.controller('DashboardCtrl', ['$scope', '$http', '$filter', function ($scope, $http, $filter) {

    $http({
		method: 'GET',
	  	url: base_url + 'backend2/api/websites/get/'
	}).then(function successCallback(response) {
		response.data.forEach(function(v, i) {
			response.data[i].url = formatUrl(v.url);
		});
	    $scope.all_websites = response.data;
	    displayWebsite(response.data[0]._id);
	  }, function errorCallback(response) {
	    // called asynchronously if an error occurs
	    // or server returns response with an error status.
	  });

	$scope.changeWebsite = function(id) {
		console.log(id);
		displayWebsite(id);
	};

	$scope.addKeyword = function() {
		$scope.website_keywords_max = 1.5 * $scope.website_keywords_max;
		$scope.keywords[$scope.new_keyword] = $scope.website_keywords_max;
	}

	$scope.addAd = function() {
		var new_ad = $scope.new_ad;
		var id = $scope.active._id;
		$http({
	  		method: 'GET',
	  		url: base_url + 'backend2/api/adtext/addAd/' + id + '/',
	  		params: {
	  			title: new_ad.title,
	  			desc1: new_ad.desc[0],
	  			desc2: new_ad.desc[1],
	  			fiability: new_ad.fiability,
	  			clicks: new_ad.clicks
	  		}
		}).then(function successCallback(response) {
				$scope.active.ads.push($scope.new_ad);

		  	}, function errorCallback(response) {
		    	// called asynchronously if an error occurs
		    	// or server returns response with an error status.
		  	});
	}

	function displayWebsite(id) {
		$http({
	  		method: 'GET',
	  		url: base_url + 'backend2/api/websites/get/' + id + '/'
		}).then(function successCallback(response) {
				response.data[0]["url"] = formatUrl(response.data[0]["url"]);
				console.log(response.data);
				var website = response.data[0];
				var website_keywords = sliceDictionnary(website.keywords, 20);
				var minmax = getMinMax(website_keywords);
				$scope.website_keywords_min = minmax["min"];
				$scope.website_keywords_max = minmax["max"];
				$scope.website_keywords_mean = minmax["mean"];
				$scope.active = website;
				$scope.keywords = website_keywords;

				getClosestWebsites(id);

		  	}, function errorCallback(response) {
		    	// called asynchronously if an error occurs
		    	// or server returns response with an error status.
		  	});
	}

	function getClosestWebsites(id) {
		$http({
	  		method: 'GET',
	  		url: base_url + 'backend2/api/websites/closest/' + id + '/'
		}).then(function successCallback(response) {
				response.data.forEach(function(v, i) {
					response.data[i].data.url = formatUrl(v.data.url);
				});
				$scope.closest = response.data;

		  	}, function errorCallback(response) {
		    	// called asynchronously if an error occurs
		    	// or server returns response with an error status.
		  	});		
	}

	function formatUrl(url) {
		url = url.split("/")[2].split("www.")[1];
		return url;
	}

	function sliceDictionnary(dict, limit) {
		var new_dict = {};
		var i = 0;
		for(var k in dict) {
			if(i < limit) {
				new_dict[k] = parseFloat(dict[k]);
				i++;
			}
		}
		return new_dict;
	}

	function getMinMax(keywords) {
		var max = 0;
		var min = 1;
		var sum = 0;
		var cpt = 0;
		var current;
		for(var k in keywords) {
			current = parseFloat(keywords[k]);
			if(current > max) max = current;
			if(current < min) min = current;
			sum += current;
			cpt ++;
		}

		return {"min": min, "max": max, "mean": sum / cpt};
	}

}]);

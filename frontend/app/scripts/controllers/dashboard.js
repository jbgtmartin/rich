'use strict';


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
		displayWebsite(id);
	};

	$scope.addKeyword = function() {
		$scope.website_keywords_max = 1.5 * $scope.website_keywords_max;
		$scope.keywords[$scope.new_keyword] = $scope.website_keywords_max;
	};

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
				if(typeof $scope.active.ads === 'undefined')
					$scope.active.ads = [];
				$scope.active.ads.push($scope.new_ad);
				delete $scope.new_ad;

		  	}, function errorCallback(response) {
		    	// called asynchronously if an error occurs
		    	// or server returns response with an error status.
		  	});
	};

	$scope.generateAd = function() {
		var id = $scope.active._id;
		$http({
	  		method: 'GET',
	  		url: base_url + 'backend2/api/adtext/appendClosestAdd/' + id + '/'
		}).then(function successCallback(response) {
				if(typeof $scope.active.ads === 'undefined')
					$scope.active.ads = [];
				$scope.active.ads.push(response.data);

		  	}, function errorCallback(response) {
		    	// called asynchronously if an error occurs
		    	// or server returns response with an error status.
		  	});
	};

	$scope.removeAllAds = function() {
		var id = $scope.active._id;
		$http({
	  		method: 'GET',
	  		url: base_url + 'backend2/api/adtext/removeAds/' + id + '/'
		}).then(function successCallback(response) {
			delete $scope.active.ads;

		  	}, function errorCallback(response) {
		    	// called asynchronously if an error occurs
		    	// or server returns response with an error status.
		  	});		
	};

	function displayWebsite(id) {
		$http({
	  		method: 'GET',
	  		url: base_url + 'backend2/api/websites/get/' + id + '/'
		}).then(function successCallback(response) {
				response.data[0].url = formatUrl(response.data[0].url);
				var website = response.data[0];
				var website_keywords = sliceDictionnary(website.keywords, 20);
				var minmax = getMinMax(website_keywords);
				if(typeof website.ads !== 'undefined')
					website.ads.forEach(function(v, i) {
						website.ads[i].fiability = parseFloat(v.fiability);
					});
				$scope.website_keywords_min = minmax.min;
				$scope.website_keywords_max = minmax.max;
				$scope.website_keywords_mean = minmax.mean;
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

'use strict';

app.filter('normalize', function() {
	return function(weight, min, max) {
		max = Math.min(max, max/10);
		var interval = max - min;
		return Math.min(100, (weight - min + max/10) / (interval + max/10) * 100);
	};
});
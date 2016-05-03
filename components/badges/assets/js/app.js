var appBadges = angular.module('appBadges', ['ngTable']).
	controller('BadgesCtrl', function ($scope, ngTableParams, $sce, $http) {

		$scope.bages = [{
			id                   : 0,
			assign_badge_achive  : "badge_like_number",
			badge_comments_number: "0",
			badge_dislike_number : "0",
			badge_icon_path      : "",
			badge_like_number    : "0",
			badge_name           : "",
			number               : 0,
			edit                 : false
		}];


		$scope.getBadges = function () {

			$http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";

			$http({
				method: 'POST',
				url   : decomSettings.admin_ajax,
				data  : 'action=decom_badges&f=get_badges',
			}).success(function (a) {
				$scope.bages = a.result;
				$scope.tableParams = new ngTableParams({
					page : 1,            // show first page
					count: 100           // count per page
				}, {
					total  : 0, // length of data
					getData: function ($defer, params) {
						//$defer.resolve($scope.bages);
						params.total($scope.bages.length);

						// set new data
						$defer.resolve($scope.bages);
					}
				});
			});
		}


		$scope.deleteBadge = function (badge) {
			//console.log(element.parent());
			$http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";

			$http({
				method: 'POST',
				url   : decomSettings.admin_ajax,
				data  : 'action=decom_badges&f=delete_badges&id=' + badge.id,
			}).success(function (data) {
				console.log(data);

				var index = $scope.bages.indexOf(badge);
				$scope.bages.splice(index, 1);
				badge.edit = false;

				$scope.getBadges();
			});

			return false;

		};

		$scope.saveBadge = function (badge) {
			badge.edit = false;

			// console.log(badge);
			$http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";

			var data = '&badge_name=' + badge.badge_name;
			data += '&badge_icon_path=' + badge.badge_icon_path;
			data += '&assign_badge_achive=' + badge.assign_badge_achive;
			data += '&number=' + badge.number;
			data += '&id=' + badge.id;

			$http({
				method: 'POST',
				url   : decomSettings.admin_ajax,
				data  : 'action=decom_badges&f=add_badges' + data,
			}).success(function (data) {
				console.log(data);
				$scope.getBadges();
			});

			return false;
		}

		$scope.editBadge = function (badge) {
			badge.edit = true;
			return false;
		}

		$scope.addBadge = function () {
			$scope.bages.unshift({
				id                   : 0,
				assign_badge_achive  : "badge_like_number",
				badge_comments_number: "0",
				badge_dislike_number : "0",
				badge_icon_path      : "",
				badge_like_number    : "0",
				badge_name           : "",
				number               : 0,
				edit                 : true
			});


			/*
			 $scope.tableParams = new ngTableParams({
			 page : 1,            // show first page
			 count: 2           // count per page
			 }, {
			 total  : $scope.bages.length, // length of data
			 getData: function ($defer, params) {
			 $defer.resolve($scope.bages.slice((params.page() - 1) * params.count(), params.page() * params.count()));
			 }
			 });
			 */

		}

	});
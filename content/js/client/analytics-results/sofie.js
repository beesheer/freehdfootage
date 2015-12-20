'use strict';

var app = angular.module('app', ['ngRoute', 'ui.bootstrap']);

app.config(['$routeProvider', function ($routeProvider) {
		$routeProvider.when('/bestAnswer', {
			controller: 'bestAnswer',
			templateUrl: '/js/survey-quiz/templates/bestAnswer.html'
		}).when('/allAnswers', {
			controller: 'allAnswers',
			templateUrl: '/js/survey-quiz/templates/allAnswers.html'
		}).when('/clientFeedback', {
			controller: 'clientFeedback',
			templateUrl: '/js/survey-quiz/templates/clientFeedback.html'
		}).when('/allAnswers/page/:id', {
			controller: 'allAnswerPaginator',
			templateUrl: '/js/survey-quiz/templates/allAnswers.html'
		}).when('/bestAnswer/page/:id', {
			controller: 'bestAnswerPaginator',
			templateUrl: '/js/survey-quiz/templates/bestAnswer.html'
		}).otherwise({
			controller: 'bestAnswer',
			templateUrl: '/js/survey-quiz/templates/bestAnswer.html'
		});
	}]);


app.factory('services', ['$rootScope', '$http', function ($rootScope, $http) {
		$rootScope.service = {
			getSofieAnswers: function (param, skip) {
				if (typeof (skip) === "undefined") {
					skip = 0;
				}
				if (param === "all") {
					$http.get('/admin/survey-quiz/get-sofie-answers?data=all_answers&skip=' + skip).success(function (e) {
						$rootScope.sofieAnswers = e;
						$rootScope.$emit("sofie_all_answers");
					});
				}
				else if (param === "best")
				{
					$http.get('/admin/survey-quiz/get-sofie-answers?data=best_answer&skip=' + skip).success(function (e) {
						$rootScope.sofieBestAnswers = e;
						$rootScope.$emit("sofie_best_answers");
					});
				}
			},
			getSofieCount: function () {
				$http.get('/admin/survey-quiz/count').success(function (e) {
					$rootScope.count = e;
					$rootScope.$emit("get_count");
				});
			},
			getFeedback: function () {
				$http.get('/admin/survey-quiz/get-feedback').success(function (e) {
					$rootScope.feedbackData = e;
					$rootScope.$emit("get_feedback");
				});
			}
		};
		return $rootScope.service;
	}]);

app.controller('bestAnswerPaginationController', ['$scope', 'services', '$rootScope', '$routeParams', '$location', function ($scope, services, $rootScope, $routeParams, $location) {
		$rootScope.$on("get_count", function () {
			$scope.allCount = $rootScope.count.best_answer;
			$scope.maxSize = 5;
			$scope.currentPage = 1;
			if(typeof($routeParams.id)!=="undefined"){
				$scope.currentPage = ($routeParams.id/10);
			}
		});

		$scope.$watch('currentPage', function (newPage) {
			if (newPage >= 1) {
				$location.url("/bestAnswer/page/"+(newPage*10));
			}
		});

		$scope.pageChanged = function () {
			console.log(this);
		};
	}]);

app.controller('bestAnswer', ['$scope', 'services', '$rootScope', function ($scope, services, $rootScope) {
		services.getSofieAnswers("best");

		$rootScope.$on("sofie_best_answers", function () {
			$scope.bestAnswers = $rootScope.sofieBestAnswers;
		});
		services.getSofieCount();
		$rootScope.$on("get_count", function () {
			$scope.allCount = $rootScope.count.best_answer;
		});
		$("#bestAnswer").attr("checked", true);
		$("#allAnswers").attr("checked", false);
		$("#clientFeedback").attr("checked", false);
		$scope.message = "best answer message";
	}]);

app.controller('allAnswerPaginationController', ['$scope', 'services', '$rootScope', '$routeParams', '$location', function ($scope, services, $rootScope, $routeParams, $location) {
		$rootScope.$on("get_count", function () {
			$scope.allCount = $rootScope.count.all_answers;
			$scope.maxSize = 5;
							$scope.currentPage = 1;
			if(typeof($routeParams.id)!=="undefined"){
				$scope.currentPage = ($routeParams.id/10);
			}
		});

		$scope.$watch('currentPage', function (newPage) {
			if (newPage >= 1) {
				$location.url("/allAnswers/page/"+(newPage*10));
			}
		});

		$scope.pageChanged = function () {
			console.log(this);
		};
	}]);

app.controller('allAnswers', ['$scope', 'services', '$rootScope', '$routeParams', function ($scope, services, $rootScope, $routeParams) {
		services.getSofieAnswers("all");
		$rootScope.$on("sofie_all_answers", function () {
			$scope.sofieAllAnswers = $rootScope.sofieAnswers;
		});
		$scope.currentPage = $routeParams.id;
		$scope.maxSize = 5;
		services.getSofieCount();
		$rootScope.$on("get_count", function () {
			$scope.allCount = $rootScope.count.all_answers;
		});

		$("#bestAnswer").attr("checked", false);
		$("#allAnswers").attr("checked", true);
		$("#clientFeedback").attr("checked", false);

		$scope.message = "allAnswers";
	}]);

app.controller('clientFeedback', ['$scope', '$rootScope', 'services', function ($scope, $rootScope, services) {
		services.getFeedback();
		$rootScope.$on("get_feedback", function () {
			$scope.feedbackData = $rootScope.feedbackData;
		});
		$("#bestAnswer").attr("checked", false);
		$("#allAnswers").attr("checked", false);
		$("#clientFeedback").attr("checked", true);
	}]);

app.controller('allAnswerPaginator', ['$scope', '$routeParams', 'services', '$rootScope', function ($scope, $routeParams, services, $rootScope) {
		services.getSofieAnswers("all", $routeParams.id);
		$rootScope.$on("sofie_all_answers", function () {
			$scope.sofieAllAnswers = $rootScope.sofieAnswers;
		});
		services.getSofieCount();
		$rootScope.$on("get_count", function () {
			$scope.allCount = $rootScope.count.all_answers;
		});
	}]);

app.controller('bestAnswerPaginator', ['$scope', '$routeParams', 'services', '$rootScope', function ($scope, $routeParams, services, $rootScope) {
		services.getSofieAnswers("best", $routeParams.id);
		$rootScope.$on("sofie_best_answers", function () {
			$scope.sofieBestAnswers = $rootScope.sofieBestAnswers;
		});
		services.getSofieCount();
		$rootScope.$on("get_count", function () {
			$scope.allCount = $rootScope.count.best_answer;
		});
	}]);

app.controller('main', ['$scope', '$location', function ($scope, $location) {
		$scope.message = "hello world";

		$scope.bestAnswer = function () {
			$location.url("/bestAnswer");
		};

		$scope.allAnswers = function () {
			$location.url("/allAnswers");
		};

		$scope.clientFeedback = function () {
			$location.url("/clientFeedback");
		};
	}]);
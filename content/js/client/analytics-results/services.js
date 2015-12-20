/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
'use strict';

angular.module('app').factory('services', [
	'$rootScope', '$http', function ($rootScope, $http) {
		$rootScope.selectedSurveys = [];
		$rootScope.service = {
			getFeedbackData: function () {
				if (typeof (window.dateFrom) !== "undefined" && typeof (window.dateTo) !== "undefined") {
					$.getJSON('/client/analytics-results/get-feedback?date_from=' + window.dateFrom + "&date_to=" + window.dateTo + "&client=" + window.allClients).success(function (e) {
						$rootScope.feedbackData = e;
						$rootScope.$emit("feedback_received");
					});
				} else
				{
					$.getJSON('/client/analytics-results/get-feedback').success(function (e) {
						$rootScope.feedbackData = e;
						$rootScope.$emit("feedback_received");
					});
				}
			},
			getQuizData: function () {
				if (typeof (window.dateFrom) !== "undefined" && typeof (window.dateTo) !== "undefined") {
					$.getJSON('/client/analytics-results/quiz-results?date_from=' + window.dateFrom + "&date_to=" + window.dateTo + "&client=" + window.allClients).success(function (e) {
						$rootScope.quizData = e;
						$rootScope.$emit("quiz_received");
					});
				} else
				{
					$.getJSON('/client/analytics-results/quiz-results').success(function (e) {
						$rootScope.quizData = e;
						$rootScope.$emit("quiz_received");
					});
				}
			},
			getSurveyTotals: function () {
				$http.get('/client/analytics-results/survey-totals').success(function (e) {
					$rootScope.surveyTotals = e;
					$rootScope.$emit("survey_totals");

				});
			},
			getUserInfo: function (f) {
				$.ajax({
					url: '/client/analytics-results/quiz-result-for-user',
					dataType: "json",
					data: "user_id=" + f,
					success: function (g) {
						$rootScope.userInfo = g;
						$rootScope.$emit("user_info_received");
					}
				});
			},
            getSurveyData: function () {
                if (typeof (window.dateFrom) !== "undefined" && typeof (window.dateTo) !== "undefined") {
                    if(typeof(window.allClients)==="undefined"){
                        window.allClients = "";
                    }
                    $http.get('/client/analytics-results/get-surveys?date_from=' + window.dateFrom + "&date_to=" + window.dateTo + "&client=" + window.allClients).success(function (e) {
                        $rootScope.surveyData = e;
                        $rootScope.$emit("survey_received");
                    });
                } else
                {
                    $http.get('/client/analytics-results/get-surveys').success(function (e) {
                        $rootScope.surveyData = e;
                        $rootScope.$emit("survey_received");
                    });
                }
            },
			getSurveySummary: function () {
				if (typeof ( window.dateFrom) !== "undefined" && typeof (window.dateTo) !== "undefined" ) {
					$http.get('/client/analytics-results/get-survey-summary?date_from=' + window.dateFrom + '&date_to=' + window.dateTo + "&client=" + window.allClients).success(function (e) {
						$rootScope.surveySummary = e;
						$rootScope.$emit("survey_summary_received");
					});
				} else
				{
					$http.get('/client/analytics-results/get-survey-summary').success(function (e) {
						$rootScope.surveySummary = e;
						$rootScope.$emit("survey_summary_received");
					});
				}
			},
			getSurveyDetails: function () {
				if (typeof (window.dateFrom) !== "undefined" && typeof (window.dateTo) !== "undefined") {
					$http.get('/client/analytics-results/get-survey-details?date_from=' + window.dateFrom + '&date_to=' + window.dateTo + "&client=" + window.allClients).success(function (e) {
						$rootScope.surveyDetails = e;
						$rootScope.$emit("survey_details_received");
					});
				} else
				{
					$http.get('/client/analytics-results/get-survey-details').success(function (e) {
						$rootScope.surveyDetails = e;
						$rootScope.$emit("survey_details_received");
					});
				}
			},
			getAllSurveys: function () {
				if (typeof (window.dateFrom) !== "undefined" && typeof (window.dateTo) !== "undefined") {
					$http.get('/client/analytics-results/get-survey?date_from=' + window.dateFrom + "&date_to=" + window.dateTo + "&client=" + window.allClients).success(function (e) {
						$rootScope.survey = e;
						$rootScope.$emit("surveys_received");
					});
				} else
				{
					$http.get('/client/analytics-results/get-survey').success(function (e) {
						$rootScope.survey = e;
						$rootScope.$emit("surveys_received");
					});
				}
			},
			getAllClients: function () {
				if (typeof (window.dateFrom) !== "undefined" && typeof (window.dateTo) !== "undefined") {
					$http.get('/client/analytics-results/get-client?date_from=' + window.dateFrom + "&date_to=" + window.dateTo + "&client=" + window.allClients).success(function (e) {
						$rootScope.client = e;
						$rootScope.$emit("clients_received");
					});
				} else
				{
					$http.get('/client/analytics-results/get-client').success(function (e) {
						$rootScope.client = e;
						$rootScope.$emit("clients_received");
					});
				}
			},
            getCumulativeSummary: function () {

                if (typeof (window.dateFrom) !== "undefined" && typeof (window.dateTo) !== "undefined") {
                    $http.get('/client/analytics-results/get-cumulative-summary?date_from=' + window.dateFrom + "&date_to=" + window.dateTo + "&client=" + window.clientId).success(function (e) {
                        $rootScope.cumulatives = e;
                        $rootScope.$emit("cumulatives_received");
                    });
                } else {
                    $http.get('/client/analytics-results/get-cumulative-summary').success(function (e) {
                        $rootScope.cumulatives = e;
                        $rootScope.$emit("cumulatives_received");
                    });
                }
            },
            printCumulativeData: function() {
                var data = window.printingData;
                /*
                $.ajax({
                    url: '/client/analytics-results/print-to-excel',
                    dataType: "json",
                    method: "POST",
                    data: data[0],
                    filename: data[1],
                    dates: data[2],
                    success: function (g) {
                        $rootScope.$emit("printout_done");
                    }
                });
                */
                $http.get('/client/analytics-results/print-to-excel?date_from=' + window.dateFrom + "&date_to=" + window.dateTo + "&client=" + window.clientId).success(function (e) {
                    $rootScope.$emit("printout_done");
                });
            }
		};

		return $rootScope.service;
	}
]);


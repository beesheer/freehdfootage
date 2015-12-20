/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

'use strict';

var app = angular.module('app', ['angular.filter','ngSanitize']);

app.filter('allowhtml', function($sce) { return $sce.trustAsHtml; });

app.controller('displayQuiz', ['$scope', '$rootScope', 'services', function ($scope, $rootScope, services) {

		$rootScope.$on("quiz_received", function (e) {
			var data = $rootScope.quizData;

			var uniqueSurveys = [];
			$scope.$apply(function () {
				for (var j in data) {
					if (uniqueSurveys.indexOf(data[j].name) === -1) {
						uniqueSurveys.push(data[j].name);
					}
				}
				$scope.expandOne = function ($index) {
					$scope.activePositionOne = $index;
					for (var k in $rootScope.quizData) {
						if ($rootScope.quizData[k].name === $index) {
							if ($rootScope.quizData[k].checked === 0) {
								$rootScope.quizData[k].checked = 1;
							} else
							{
								$rootScope.quizData[k].checked = 0;
							}
						}
					}
				};
				$scope.uniqueSurveys = uniqueSurveys;
				for (var t in $rootScope.quizData) {
					$rootScope.quizData[t].checked = 1;
				}
				$scope.allData = $rootScope.quizData;
			});
		});

		$scope.userInfo = function (h) {
			services.getUserInfo(h);
		};

		$scope.expand = function ($index) {
			$scope.activePosition = $index;
		};
		$scope.hideShow = function (e) {
			if (e.checked === 0) {
				e.checked = 1;
			}
			else
			{
				e.checked = 0;
			}
		};
		$rootScope.$on("user_info_received", function () {
			$scope.$apply(function () {
				$scope.userData = $rootScope.userInfo;
			});
		});
	}]);

app.controller('app', [
	'$scope', '$rootScope', 'services', function ($scope, $rootScope, services) {

		$rootScope.$on("feedback_received", function (e) {
			$scope.$apply(function () {
				var data = $rootScope.feedbackData;
				var currentSurvey = [];


				for (var m in data) {
					if (currentSurvey.indexOf(data[m].survey_name) === -1) {
						currentSurvey.push(data[m].survey_name);
					}
					else
					{
						data[m].survey_name = "";
					}
					data[m].display = '0';
				}
				$scope.data = data;
			});
		});

		$scope.showHide = function (e) {
			if (e.display === 0) {
				e.display = 1;
			}
			else
			{
				e.display = 0;
			}
			if (e === "all") {
				for (var m in $scope.data) {
					if ($scope.data[m].display === 0) {
						$scope.data[m].display = 1;
					}
					else
					{
						$scope.data[m].display = 0;
					}
				}
			}
		};

		$('tr').unbind('click');
		$('body').unbind('click');
	}
]);

app.controller('survey', [
	'$scope', '$rootScope', 'services', function ($scope, $rootScope, services) {
		$scope.test = "hello world";
		//services.getSurveyData();
		$rootScope.$on("survey_received", function (e) {
			$scope.$apply(function () {
				var data = $rootScope.surveyData;
				$scope.data = data;
			});
		});
	}
]);

app.controller('displaySurvey', [
	'$scope', '$rootScope', 'services', function ($scope, $rootScope, services) {
		//services.getSurveyData();
		$rootScope.$on("survey_received", function (e) {

			var allData = $rootScope.surveyData;
			$scope.$apply(function () {
				var uniqueSurveys = [];
				var uniqueQuestion = [];
				var uniqueOptions = [];
				for (var j in allData)
				{
					if (uniqueSurveys.indexOf(allData[j].survey_name) === -1) {
						uniqueSurveys.push(allData[j].survey_name);
					}
					if (uniqueQuestion.indexOf(allData[j].question) === -1) {
						uniqueQuestion.push(allData[j].question);
					}
					if (uniqueOptions.indexOf(allData[j].option) === -1) {
						uniqueOptions.push(allData[j].option);
					}
					allData[j].checked = true;
				}

				$scope.uniqueSurveys = uniqueSurveys;
				$scope.uniqueQuestion = uniqueQuestion;
				$scope.uniqueOptions = uniqueOptions;
				$scope.allData = allData;
			});
		});
		services.getSurveyTotals();
		$rootScope.$on("survey_totals", function (e) {
			var surveyTotals = $rootScope.surveyTotals;
			$scope.$apply(function () {
				$scope.surveyTotals = surveyTotals;
			});
		});

	}
]);

app.controller('surveySummary', ['$scope', '$rootScope', 'services','$sanitize', function ($scope, $rootScope, services,$sanitize) {
		$scope.name = 'car';
		//services.getSurveySummary();
		$rootScope.$on("survey_summary_received", function () {
			$scope.data = $rootScope.surveySummary;
			for(var ll in $scope.data){
				$scope.data[ll].question = $sanitize($scope.data[ll].question);
				$scope.data[ll].data = $sanitize($scope.data[ll].data);
			}

			var counter = [];
			var myData = $scope.data;

			// to calculate totals , which then can be used for percentages
			for (var p in myData) {
				if (counter.indexOf(myData[p].question_id) === -1) {
					counter.push(myData[p].question_id);
					counter[myData[p].question_id] = parseInt(myData[p].count);
				} else
				{
					counter[myData[p].question_id] += parseInt(myData[p].count);
				}
				myData[p].hide = 0;
				myData[p].checked = true;
			}
			$rootScope.surveySummary = $scope.data;
			$scope.totals = counter;
		});

		$scope.hideShow = function (e) {
			if (e.hide === 0) {
				e.hide = 1;
			}
			else
			{
				e.hide = 0;
			}
		};

        $scope.showPrintableSummary = function( pageId ) {
            //
        };

	}]);

app.controller('surveyDetails', ['$scope', '$rootScope', 'services','$sanitize', function ($scope, $rootScope, services,$sanitize) {
		//services.getSurveyDetails();
		$rootScope.$on("survey_details_received", function () {
			$scope.data = $rootScope.surveyDetails;
			for (var j in $scope.data) {
				if ($rootScope.selectedSurveys.indexOf($scope.data[j].name) === -1) {
					$rootScope.selectedSurveys.push($scope.data[j].name);
					$rootScope.selectedSurveys[$scope.data[j].name] = true;
				}
				$scope.data[j].display = 0;
				//Sanitization
				$scope.data[j].question = $sanitize($scope.data[j].question);
				$scope.data[j].data = $sanitize($scope.data[j].data);
			}
		});

		$scope.showHide = function (e) {
			if (e.display === 0) {
				e.display = 1;
			}
			else
			{
				e.display = 0;
			}
		};

	}]);



app.controller('main', ['$scope', '$rootScope', 'services', function ($scope, $rootScope, services) {

		$scope.surveyQuizVal = "Survey";
		$scope.consolidatedDetailVal = "Consolidated";
		var todaysDate = new Date();
		var oneWeekAgo = new Date();
		oneWeekAgo.setDate(todaysDate.getDate() - 7);
		window.dateFrom = oneWeekAgo.getFullYear() + "-" + (oneWeekAgo.getMonth() + 1) + "-" + oneWeekAgo.getDate();
		window.dateTo = todaysDate.getFullYear() + "-" + (todaysDate.getMonth() + 1) + "-" + todaysDate.getDate();
		services.getAllClients();
		services.getSurveyData();
		services.getSurveySummary();
		services.getSurveyDetails();
        services.getCumulativeSummary();
		$scope.name = "hello world";
		$rootScope.hideAll = 0;
		$rootScope.expandAll = function () {
			$rootScope.hideAll = 0;
			if (typeof ($scope.surveySummary) !== "undefined") {
				$scope.surveySummary.forEach(function (j) {
					j.hide = 0;
				});
			}

			if (typeof ($scope.surveyDetails) !== "undefined") {
				$scope.surveyDetails.forEach(function (j) {
					j.display = 1;
				});
			}
			if (typeof ($scope.quizData) !== "undefined") {
				$scope.quizData.forEach(function (j) {
					j.checked = 1;
				});
			}
			if (typeof ($scope.feedbackData) !== "undefined") {
				$scope.feedbackData.forEach(function (j) {
					j.display = 1;
				});
			}
            if (typeof ($scope.cumulatives) !== "undefined") {
                $scope.cumulatives.forEach(function (j) {
                    j.display = 1;
                });
            }
		};

		$rootScope.collapseAll = function (e) {
			$rootScope.hideAll = 1;
			$scope.activePosition = -1;
			$scope.activePositionOne = -1;
			if (typeof ($scope.hideShow) !== "undefined") {
				for (var j in $scope.hideShow) {
					$scope.hideShow[j] = false;
				}
			}
			if (typeof ($scope.surveySummary) !== "undefined") {
				$scope.surveySummary.forEach(function (j) {
					j.hide = 1;
				});
			}

			if (typeof ($scope.surveyDetails) !== "undefined") {
				$scope.surveyDetails.forEach(function (j) {
					j.display = 0;
				});
			}
			if (typeof ($scope.quizData) !== "undefined") {
				$scope.quizData.forEach(function (j) {
					j.checked = 0;
				});
			}
			if (typeof ($scope.feedbackData) !== "undefined") {
				$scope.feedbackData.forEach(function (j) {
					j.display = 0;
				});
			}
            if (typeof ($scope.cumulatives) !== "undefined") {
                $scope.cumulatives.forEach(function (j) {
                    j.display = 0;
                });
            }
		};

		$scope.surveyQuiz = function () {

			if (this.surveyQuizVal === "Survey") {
				$("#survey-results").addClass("highlighted");
			} else {
				$("#survey-results").removeClass("highlighted");
				$("#sofie-results").removeClass("highlighted");
			}
			if (this.surveyQuizVal === "Quiz") {
				$("#quiz-results").addClass("highlighted");
			} else {
				$("#quiz-results").removeClass("highlighted");
				$("#sofie-results").removeClass("highlighted");
			}
			$(".regular").find("font").html(this.surveyQuizVal + " Results");
		};

		$scope.consolidatedDetail = function () {
			console.log($scope.consolidatedDetailVal);
		};
	}]);

app.controller('cumulativeSummary', ['$scope', '$rootScope', '$http', 'services','$sanitize', function ($scope, $rootScope, $http, services,$sanitize) {

    $scope.Math = window.Math;
    $scope.summaryTabVal = "Averages";

    $rootScope.$on("cumulatives_received", function (e) {

        var cumulatives = $rootScope.cumulatives;
            for (var j in cumulatives) {
                cumulatives[j].checked = true;
         }
        $scope.cumulatives = $rootScope.cumulatives;
       //questionOptions
        //$("#cumulativeDump").html(JSON.stringify($rootScope.cumulatives));
    });

    $scope.showHide = function (e) {
        if (e.display === 0) {
            e.display = 1;
        }
        else
        {
            e.display = 0;
        }
    };
    $scope.summaryTab = function ( tabName ) {
        $scope.summaryTabVal = tabName;
    };

    $scope.exportToExcel = function( data ) {

        switch( data.type ) {
            case "quiz":
                switch ($scope.summaryTabVal ) {
                    case "Averages":
                        var quizResultsGeneral      =   data['general'];

                        var ttl_started             =   quizResultsGeneral['total_participants'];
                        var ttl_completed           =   quizResultsGeneral['total_completed'];
                        var pctCompleted            =   Math.round((ttl_completed/ttl_started)*100) + "%";

                        var Tab1_column_heads        =   data['heads_general'];
                        var Tab1_column_Started      =   ttl_started;
                        var Tab1_column_Completed    =   ttl_completed + '( ' + pctCompleted + ' )';

                        var Tab1_column_Passed       =   (ttl_completed==0) ? 0 : quizResultsGeneral['total_passed'] + '( ' + Math.round(Number( (quizResultsGeneral['total_passed']/ttl_started)*100)) + '%  )';
                        var Tab1_column_Score        =   quizResultsGeneral['average_score'];
                        var Tab1_column_Time         =   quizResultsGeneral['average_time'];

                        var Tab1_print_data          =   [Tab1_column_heads,[ Tab1_column_Started,Tab1_column_Completed,Tab1_column_Passed,Tab1_column_Score,Tab1_column_Time]];

                        $scope.printToExcelData = JSON.stringify( Tab1_print_data );
                        $scope.printToExcelDates = JSON.stringify( [window.dateFrom,window.dateTo] );
                        $scope.printToExcelFileName = "CourseAverages";

                        break;

                    case "Questions":
                        var quizResultsQuestion = data['question'];
                        var Tab2_column_heads          =   data['heads_question'];
                        var Tab2_data                  =   [];
                        var Tab2_row                   =   [];
                        Tab2_data[0] = Tab2_column_heads;
                        for(var i=0; i<quizResultsQuestion.length; i++) {
                            var Question   =   quizResultsQuestion[i]['questionText'];
                            var Answer     =   quizResultsQuestion[i]['questionAnswer'];
                            var Least      =   quizResultsQuestion[i]['answerLeast'] + " (" + Math.round(Number(quizResultsQuestion[i]['pctLeast']*100)) + "%)";
                            var Most       =   quizResultsQuestion[i]['answerMost'] + " (" + Math.round(Number(quizResultsQuestion[i]['pctMost']*100)) + "%)";
                            var AvgTime    =   quizResultsQuestion[i]['avgTime'];
                            var Longest    =   quizResultsQuestion[i]['mostTime'];
                            var Quickest   =   quizResultsQuestion[i]['leastTime'];
                            var AvgCorrect =   Math.round(Number(quizResultsQuestion[i]['avgCorrect']*100));
                            Tab2_row   =   [Question,Answer,Least,Most,AvgTime,Longest,Quickest,AvgCorrect];
                            Tab2_data.push(Tab2_row);
                        }

                        var Tab2_print_data = Tab2_data;

                        $scope.printToExcelData = JSON.stringify( Tab2_print_data );
                        $scope.printToExcelDates = JSON.stringify( [window.dateFrom,window.dateTo] );
                        $scope.printToExcelFileName = "QuestionHistory";

                        break;

                    case "Students":
                        var quizResultsStudent = data['student'];
                        var Tab3_column_heads           =   data['heads_student'];//['Name','Email','Registered','Completed','Passed','Score','Time (seconds)','Started','Last Activity'];
                        var Tab3_data                   =   [];
                        var Tab3_row                    =   [];
                        Tab3_data[0]                    = Tab3_column_heads;
                        for(var i=0; i<quizResultsStudent.length; i++) {
                            var Name               =   quizResultsStudent[i]['first'] + " " + quizResultsStudent[i]['last'];
                            var Email              =   quizResultsStudent[i]['email'];
                            var Registration       =   quizResultsStudent[i]['registered'].substring( 0,10 );
                            var Completed          =   Number(quizResultsStudent[i]['complete']);
                            var Passed             =   (Completed==1) ? quizResultsStudent[i]['passed'] : "n/a";
                            var Score              =   (Completed==1) ? quizResultsStudent[i]['score'] : "n/a";
                            var Time               =   quizResultsStudent[i]['time'];
                            var Start              =   quizResultsStudent[i]['start'].substring( 0,10 );
                            var Last               =   quizResultsStudent[i]['lastactivity'].substring( 0,10 );
                            Tab3_row   =   [Name,Email,Registration,Completed,Passed,Score,Time,Start,Last];
                            Tab3_data.push(Tab3_row);
                        }

                        var Tab3_print_data = Tab3_data;

                        $scope.printToExcelData = JSON.stringify( Tab3_print_data );
                        $scope.printToExcelDates = JSON.stringify( [window.dateFrom,window.dateTo] );
                        $scope.printToExcelFileName = "Students";

                        break;

                }

                break;

            case "survey":
                var CEFeedback = data['data'];

                //CE Feedback tab
                var Respondents             =   data['respondents'];
                var CETab1_column_heads     =   data['heads'];
                var CETab1_data             =   [];
                var CETab1_row              =   [];

                CETab1_data[0]              = CETab1_column_heads;

                for(var i=0; i<CEFeedback.length; i++) {
                    var Question           =   CEFeedback[i]['question'];
                    //var Description        =   CEFeedback[i]['description'];
                    var Values             =   CEFeedback[i]['values'];
                    var Comments           =   CEFeedback[i]['comments'];
                    var CETab1_row         =   [Question, Values, Comments];//Description,
                    CETab1_data.push(CETab1_row);
                }

                var Tab4_print_data = CETab1_data;

                $scope.printToExcelData = JSON.stringify( Tab4_print_data );
                $scope.printToExcelDates = JSON.stringify( [window.dateFrom,window.dateTo] );
                $scope.printToExcelFileName = "Feedback";

                break;
        }

    };

}]);

app.controller('listSurvey', ['$scope', '$rootScope', 'services', function ($scope, $rootScope, services) {

		$scope.message = "test";
		services.getAllSurveys();
		services.getAllClients();
		var todaysDate = new Date();
		var oneWeekAgo = new Date();
		oneWeekAgo.setDate(todaysDate.getDate() - 7);
		$("#date_from").datepicker({timezone: "-0400"});
		$("#date_to").datepicker({timezone: "-0400"});
		$("#date_from").datepicker("setDate", oneWeekAgo);
		$("#date_from").find("span").html("From: " + oneWeekAgo.getDate() + "/" + (oneWeekAgo.getMonth() + 1) + "/" + oneWeekAgo.getFullYear());
		window.dateFrom = oneWeekAgo.getFullYear() + "-" + (oneWeekAgo.getMonth() + 1) + "-" + oneWeekAgo.getDate();
		window.dateTo = todaysDate.getFullYear() + "-" + (todaysDate.getMonth() + 1) + "-" + todaysDate.getDate();
		$("#date_to").datepicker("setDate", todaysDate);
		$("#date_to").find("span").html("To: " + todaysDate.getDate() + "/" + (todaysDate.getMonth() + 1) + "/" + todaysDate.getFullYear());
		$rootScope.$on("surveys_received", function (e) {

			var surveys = $rootScope.survey;
			for (var j in surveys) {
				surveys[j].checked = true;
			}
			$scope.surveys = $rootScope.survey;
		});

		$rootScope.$on("clients_received", function (e) {

			var clients = $rootScope.client;
			var allClients = [];
			for (var c in clients) {
				clients[c].checked = true;
				allClients.push(clients[c].id);
			}
			window.allClients = allClients;
			$scope.clients = clients;
		});
        $rootScope.$on("cumulatives_received", function (e) {

            var cumulatives = $rootScope.cumulatives;
            for (var j in cumulatives) {
                cumulatives[j].checked = true;
            }
            $scope.cumulatives = $rootScope.cumulatives;
            //$("#cumulativeDump").html(JSON.stringify($rootScope.cumulatives));
        });
		$scope.clientsClick = function (e) {
			for (var m in $scope.clients) {
				if ($scope.clients[m].name === e) {
					if ($scope.clients[m].checked === true) {
						$scope.clients[m].checked = false;
					}
					else
					{
						$scope.clients[m].checked = true;
					}
				}
			}
			var allClients = [];
			for (var j in $scope.clients)
			{
				if ($scope.clients[j].checked === true) {
					allClients.push($scope.clients[j].id);
				}
			}
			window.allClients = allClients;
		};

		$scope.dateFromClick = function () {
			$("#date_from").datepicker('show');
			$("#date_from").on("changeDate", function (e) {
				var dateFrom = new Date(e.date);
				dateFrom.setDate(dateFrom.getDate()+1);
				var origDateFrom = dateFrom;
				dateFrom = (dateFrom.getDate()) + "/" + (dateFrom.getMonth() + 1) + "/" + (dateFrom.getFullYear());
				window.dateFrom = origDateFrom.getFullYear() + "-" + (origDateFrom.getMonth() + 1) + "-" + origDateFrom.getDate();
				$("#date_from").find("span").html("From: " + dateFrom);
			});
		};

		$scope.dateToClick = function () {
			$("#date_to").datepicker('show');
			$("#date_to").on("changeDate", function (e) {
				var dateTo = new Date(e.date);
				dateTo.setDate(dateTo.getDate()+1);
				var origDateTo = dateTo;
				dateTo = (dateTo.getDate()) + "/" + (dateTo.getMonth() + 1) + "/" + (dateTo.getFullYear());
				window.dateTo = origDateTo.getFullYear() + "-" + (origDateTo.getMonth() + 1) + "-" + origDateTo.getDate();
				$("#date_to").find("span").html("To: " + dateTo);
			});
		};

		$scope.refreshAction = function () {
			services.getSurveySummary();
			services.getSurveyDetails();
			services.getFeedbackData();
			services.getQuizData();
			services.getAllSurveys();
			services.getAllClients();
            services.getCumulativeSummary();
		};

		$scope.toggleUsers = function () {
			if ($scope.showUsers === 1) {
				$scope.showUsers = 0;
			}
			else
			{
				$scope.showUsers = 1;
			}
		};

		$scope.showUsers = 0;

		$scope.bla = function (e) {
			if ($rootScope.selectedSurveys[e] === false) {
				$rootScope.selectedSurveys[e] = true;
			}
			else
			{
				$rootScope.selectedSurveys[e] = false;
			}

			for (var m in $rootScope.surveySummary) {
				if ($rootScope.surveySummary[m].name === e) {
					if ($rootScope.surveySummary[m].checked === true) {
						$rootScope.surveySummary[m].checked = false;
					}
					else
					{
						$rootScope.surveySummary[m].checked = true;
					}
				}
			}
		};
	}]);

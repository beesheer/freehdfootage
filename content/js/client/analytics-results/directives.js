/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
angular.module('app').directive('test',['$rootScope',function($rootScope){
	return {
		replace:true,
		compile: function (tElement, tAttrs) {

			$rootScope.$on("feedback_received", function (e) {
				console.log(e);
				            // this is link function
            return function (scope) {
                scope.name = "test";
				scope.data = $rootScope.quizData;
            };
		});

        },
		templateUrl:'/js/survey-quiz/templates/test.html'
	};
}]);


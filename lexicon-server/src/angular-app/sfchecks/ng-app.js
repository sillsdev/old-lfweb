'use strict';

// Declare app level module which depends on filters, and services
angular.module('sfchecks', 
		[
		 'ngSanitize',
		 'sfchecks.questions',
		 'sfchecks.question',
		 'sfchecks.filters',
		 'sfchecks.services',
		 'palaso.ui.notice'
		])
	.config(['$routeProvider', function($routeProvider) {
		//--------Do I need them for LF Comments? --->>>
	    $routeProvider.when(
    		'/projects', 
    		{
    			templateUrl: '/angular-app/sfchecks/partials/questions.html', 
    			controller: 'QuestionsCtrl'
    		}
	    );
	    $routeProvider.when(
    		'/project/:projectId', 
    		{
    			templateUrl: '/angular-app/sfchecks/partials/questions.html', 
    			controller: 'QuestionsCtrl'
    		}
    	);
	  //<<<<--------
	    
	    
	    $routeProvider.when(
    		'/project/:projectId/:entryId', 
    		{
    			templateUrl: '/angular-app/sfchecks/partials/questions.html', 
    			controller: 'QuestionsCtrl'
    		}
    	);
	    $routeProvider.when(
    		'/project/:projectId/:entryId/:entryRefKey',
    		{
    			templateUrl: '/angular-app/sfchecks/partials/question.html', 
    			controller: 'QuestionCtrl'
			}
		);
	    $routeProvider.otherwise({redirectTo: 'projects'});
	}])
	.controller('MainCtrl', ['$scope', 'silNoticeService', '$route', '$routeParams', '$location',
	                         function($scope, noticeService, $route, $routeParams, $location) {
		$scope.route = $route;
		$scope.location = $location;
		$scope.routeParams = $routeParams;
		
//		noticeService.push(noticeService.ERROR, 'Oh snap! Change a few things up and try submitting again.');
//		noticeService.push(noticeService.SUCCESS, 'Well done! You successfully read this important alert message.');
//		noticeService.push(noticeService.WARN, 'Oh snap! Change a few things up and try submitting again.');
//		noticeService.push(noticeService.INFO, 'Well done! You successfully read this important alert message.');
		
	}])
	;

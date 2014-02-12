'use strict';

angular.module(
		'settings', 
		['jsonRpc', 'ui.bootstrap', 'lf.services', 'palaso.ui.dc.entry', 'ngAnimate']
	)
	.controller('SettingsCtrl', ['$scope', 'userService', 'sessionService', 'lexEntryService', '$window', '$timeout', 
	                                 function($scope, userService, ss, lexService, $window, $timeout) {

		$scope.querySettings = function() {
			$scope.config = lexService.projectSettings();
		};
		
		$scope.querySettings();
		
		$scope.editWritingSystemsCollapsed = true;
		
		$scope.editWritingSystemsCollapsed = function() {
			$scope.editWritingSystemsCollapsed = false;
		};
		
		$scope.saveWritingSystems = function() {
			$scope.editWritingSystemsCollapsed = true;
		};
		
	}])
	;

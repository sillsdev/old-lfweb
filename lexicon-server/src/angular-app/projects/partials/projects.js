'use strict';

angular.module(
		'lfprojects.projects',
		[ 'lf.services', 'palaso.ui.listview', 'palaso.ui.typeahead', 'ui.bootstrap' ]
	)
	.controller('ProjectsCtrl', ['$scope', 'projectService', 'languageService', 'sessionService', 'linkService', function($scope, projectService, languageService, ss, linkService) {
		// Rights
		$scope.rights = {};
		$scope.rights.deleteOther = ss.hasRight(ss.realm.SITE(), ss.domain.PROJECTS, ss.operation.DELETE_OTHER); 
		// Listview Selection
		$scope.newProjectCollapsed = true;
		$scope.selected = [];
		$scope.updateSelection = function(event, item) {
			var selectedIndex = $scope.selected.indexOf(item);
			var checkbox = event.target;
			if (checkbox.checked && selectedIndex == -1) {
				$scope.selected.push(item);
			} else if (!checkbox.checked && selectedIndex != -1) {
				$scope.selected.splice(selectedIndex, 1);
			}
		};
		$scope.isSelected = function(item) {
			return item != null && $scope.selected.indexOf(item) >= 0;
		};
		// Listview Data
		$scope.projects = [];
		$scope.queryProjectsForUser = function() {
			console.log("queryProjectForUser()");
			projectService.list(function(result) {
				if (result.ok) {
					$scope.projects = result.data.entries;
					$scope.enhanceDto($scope.projects);
					$scope.projectCount = result.data.count;
				}
			});
		};
		// Remove
		$scope.removeProject = function() {
			console.log("removeProject()");
			var projectIds = [];
			for(var i = 0, l = $scope.selected.length; i < l; i++) {
				projectIds.push($scope.selected[i].id);
			}
			if (l == 0) {
				// TODO ERROR
				return;
			}
			projectService.remove(projectIds, function(result) {
				if (result.ok) {
					$scope.selected = []; // Reset the selection
					$scope.queryProjectsForUser();
					// TODO
				}
			});
		};
		// Add
		$scope.addProject = function() {
			console.log("addProject()");
			var model = {};
			model.id = '';
			model.projectname = $scope.projectName;
			model.languageCode = $scope.language.subtag;
			projectService.update(model, function(result) {
				if (result.ok) {
					$scope.queryProjectsForUser();
					
				}
			});
		};

		// Fake data to make the page look good while it's being designed. To be
		// replaced by real data once the appropriate API functions are writen.
		var fakeData = {
			textCount: -3,
			viewsCount: -93,
			unreadAnswers: -4,
			unreadComments: -12
		};

		$scope.getTextCount = function(project) {
			// return projects.texts.count;
			return project.textCount;
		};

		$scope.getViewsCount = function(project) {
			return fakeData.viewsCount;
		};

		
		$scope.getUnreadAnswers = function(project) {
			return fakeData.unreadAnswers;
		};

		$scope.getUnreadComments = function(project) {
			return fakeData.unreadComments;
		};
		
		$scope.enhanceDto = function(items) {
			for (var i in items) {
				items[i].url = linkService.project(items[i].id);
				items[i].urlSettings = linkService.projectSettings(items[i].id);
			}
		};

		// ----------------------------------------------------------
		// Typeahead for project selection
		// ----------------------------------------------------------
		$scope.languages = [];
		$scope.language = {};
		$scope.typeahead = {};
		$scope.typeahead.langName = '';

		$scope.queryLanguages = function(searchTerm) {
			console.log('Searching for languages matching', searchTerm);
			if (searchTerm.length < 3) {
				return;
			}
			languageService.typeahead(searchTerm, function(result) {
				console.log("languageService.typeahead(", searchTerm, ") returned:");
				console.log(result);
				if (result.ok) {
					$scope.languages = result.data.entries;
					console.log("$scope.languages is now:", $scope.languages);
					//$scope.updateSomethingInTheForm(); // TODO: Figure out what, if anything, needs to be updated when the list comes back. 2013-08 RM
				}
			});
		};

		$scope.selectLanguage = function(item) {
			console.log('selectLanguage called with args:');
			console.log(arguments);
			$scope.language = item;
			$scope.typeahead.langName = item.description[0];
		};

		$scope.languageDescription = function(language) {
			// Format a language description for display
			// Language with just one name (most common case): English
			// Language with two names: Dutch (Flemish)
			// Language with 3+ names: Romanian (Moldavian, Moldovan)
			var desc = language.description;
			var first = desc[0];
			var rest = desc.slice(1).join(', ');
			if (rest) {
				return first + " (" + rest + ")";
			} else {
				return first;
			}
		};

		$scope.deprecationWarning = function(language) {
			if (language.deprecated) {
				return " (Deprecated)";
			} else {
				return "";
			}
		};
	}])
	;

'use strict';

angular.module(
		'sfchecks.questions',
		[ 'lf.services', 'palaso.ui.listview', 'palaso.ui.typeahead', 'ui.bootstrap', 'sgw.ui.breadcrumb' ]
	)
	.controller('QuestionsCtrl', ['$scope', 'questionsService', 'questionTemplateService', '$routeParams', 'sessionService', 'linkService', 'linkServiceGwt', 'breadcrumbService', 'silNoticeService',
	                              function($scope, questionsService, qts, $routeParams, ss, linkService, linkServiceGwt, breadcrumbService, notice) {
		var projectId = $routeParams.projectId;
		var entryId = $routeParams.entryId;
		if (entryId==undefined)
		{
			entryId='';			
		}
		$scope.projectId = projectId;
		$scope.entryId = entryId;
		
		// Rights
		$scope.rights = {};
		$scope.rights.deleteOther = false; 
		$scope.rights.create = false; 
		$scope.rights.createTemplate = false; 
		$scope.rights.editOther = false; //ss.hasRight(ss.realm.SITE(), ss.domain.PROJECTS, ss.operation.EDIT_OTHER);
		$scope.rights.showControlBar = $scope.rights.deleteOther || $scope.rights.create || $scope.rights.createTemplate || $scope.rights.editOther;
		
		// Breadcrumb
		if (entryId!=''){
			breadcrumbService.set('top',
					[
					 {href: '/gwtangular/projects', label: 'My Projects'},
					 {href: '/gwtangular/sfchecks#/project/' + $routeParams.projectId, label: ''},
					 {href: '/gwtangular/sfchecks#/project/' + $routeParams.projectId + '/' + $routeParams.entryId, label: ''},
					]
			);
		}else
			{
			breadcrumbService.set('top',
					[
					 {href: '/gwtangular/projects', label: 'My Projects'},
					 {href: '/gwtangular/sfchecks#/project/' + $routeParams.projectId, label: ''},
					]
			);
			}
		// Question templates
		$scope.templates = [];
		$scope.queryTemplates = function() {
			qts.list(function(result) {
				if (result.ok) {
					$scope.templates = result.data.entries;
				}
			});
		};
		$scope.queryTemplates();

		$scope.$watch('template', function(template) {
			if (template) {
				$scope.questionTitle = template.title;
				$scope.questionDescription = template.description;
			}
		});

		// Listview Selection
		$scope.newQuestionCollapsed = true;
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
		$scope.questions = [];
		$scope.queryQuestions = function() {
			questionsService.list(projectId, entryId, function(result) {
				if (result.ok) {
					$scope.questions = result.data.entries;
					$scope.questionsCount = result.data.count;

					$scope.enhanceDto($scope.questions);
					$scope.project = result.data.project;

					breadcrumbService.updateCrumb('top', 1, {label: $scope.project.name});
					if (entryId!=''){
						$scope.text = result.data.text;
						$scope.text.url = linkServiceGwt.text(projectId, entryId);
						breadcrumbService.updateCrumb('top', 2, {label: $scope.text.title});
					}else
					{
						$scope.text = new Object();
						$scope.text.title = $scope.project.name;
					}

					var rights = result.data.rights;
					$scope.rights.deleteOther = ss.hasRight(rights, ss.domain.QUESTIONS, ss.operation.DELETE_OTHER); 
					$scope.rights.create = ss.hasRight(rights, ss.domain.QUESTIONS, ss.operation.CREATE); 
					$scope.rights.createTemplate = ss.hasRight(rights, ss.domain.TEMPLATES, ss.operation.CREATE); 
					$scope.rights.editOther = ss.hasRight(rights, ss.domain.TEXTS, ss.operation.EDIT_OTHER);
					$scope.rights.showControlBar = $scope.rights.deleteOther || $scope.rights.create || $scope.rights.createTemplate || $scope.rights.editOther;
				}
			});
		};
		// Remove
		$scope.removeQuestions = function() {
			console.log("removeQuestions()");
			var questionIds = [];
			for(var i = 0, l = $scope.selected.length; i < l; i++) {
				questionIds.push($scope.selected[i].id);
			}
			if (l == 0) {
				// TODO ERROR
				return;
			}
			questionsService.remove(projectId, questionIds, function(result) {
				if (result.ok) {
					$scope.selected = []; // Reset the selection
					$scope.queryQuestions();
					if (questionIds.length == 1) {
						notice.push(notice.SUCCESS, "The text was removed successfully");
					} else {
						notice.push(notice.SUCCESS, "The texts were removed successfully");
					}
				}
			});
		};
		// Add question
		$scope.addQuestion = function() {
			//console.log("addQuestion()");
			var model = {};
			model.id = '';
			model.entryRef = entryId;
			model.title = $scope.questionTitle;
			model.description = $scope.questionDescription;
			questionsService.update(projectId, model, function(result) {
				if (result.ok) {
					$scope.queryQuestions();
					notice.push(notice.SUCCESS, "'" + model.title + "' was added successfully");
					if ($scope.saveAsTemplate) {
						qts.update(model, function(result) {
							if (result.ok) {
								$scope.queryTemplates();
								notice.push(notice.SUCCESS, "'" + model.title + "' was added as a template question");
							}
						});
					}
					$scope.questionTitle = "";
					$scope.questionDescription = "";
					$scope.saveAsTemplate = false;
				}
			});
		};
		$scope.makeQuestionIntoTemplate = function() {
			// Expects one, and only one, question to be selected (checked)
			var l = $scope.selected.length;
			if (l != 1) {
				return;
			}
			var model = {};
			model.id = '';
			model.title = $scope.selected[0].title;
			model.description = $scope.selected[0].description;
			qts.update(model, function(result) {
				if (result.ok) {
					$scope.queryTemplates();
					notice.push(notice.SUCCESS, "'" + model.title + "' was added as a template question");
					$scope.selected = [];
				}
			});
		};

		// Fake data to make the page look good while it's being designed. To be
		// replaced by real data once the appropriate API functions are writen.
		var fakeData = {
			answerCount: -3,
			viewsCount: -27,
			unreadAnswers: -1,
			unreadComments: -5
		};

		$scope.getAnswerCount = function(question) {
			return question.answerCount;
		};

		$scope.getViewsCount = function(question) {
			return fakeData.viewsCount;
		};

		$scope.getUnreadAnswers = function(question) {
			return fakeData.unreadAnswers;
		};

		$scope.getUnreadComments = function(question) {
			return fakeData.unreadComments;
		};
		
		$scope.enhanceDto = function(items) {
			for (var i in items) {
				items[i].url = linkServiceGwt.question(projectId, items[i].entryId, items[i].entryRef);
			}
		};
	}])
	.controller('QuestionsSettingsCtrl', ['$scope', 'textService', 'sessionService', '$routeParams', 'breadcrumbService', 'silNoticeService', 
	                                      function($scope, textService, ss, $routeParams, breadcrumbService, notice) {
		var projectId = $routeParams.projectId;
		var entryId = $routeParams.entryId;
		var dto;
		$scope.projectId = projectId;
		$scope.entryId = entryId;
		$scope.editedText = {
			id: entryId,
		}

		// Breadcrumb
		breadcrumbService.set('top',
				[
				 {href: '/app/projects', label: 'My Projects'},
				 {href: '/gwtangular/sfchecks#/project/' + $routeParams.projectId, label: ''},
				 {href: '/gwtangular/sfchecks#/project/' + $routeParams.projectId + '/' + $routeParams.entryId, label: ''},
				 {href: '/gwtangular/sfchecks#/project/' + $routeParams.projectId + '/' + $routeParams.entryId + '/Settings', label: 'Settings'},
				]
		);

		// Get name from text service. This really should be in the DTO, but this will work for now.
		// TODO: Move this to the DTO (or BreadcrumbHelper?) so we don't have to do a second server round-trip. RM 2013-08
		var text;
		textService.settings_dto($scope.projectId, $scope.entryId, function(result) {
			if (result.ok) {
				$scope.dto = result.data;
				$scope.textTitle = $scope.dto.text.title;
				$scope.editedText.title = $scope.dto.text.title;
				$scope.rights = {
					editOther: ss.hasRight($scope.dto.rights, ss.domain.TEXTS, ss.operation.EDIT_OTHER),
				};
				console.log($scope.dto);
				breadcrumbService.updateCrumb('top', 1, {label: $scope.dto.bcs.project.crumb});
				breadcrumbService.updateCrumb('top', 2, {label: $scope.dto.text.title});
			}
		});

		$scope.updateText = function(newText) {
			if (!newText.content) {
				delete newText.content;
			}
			textService.update($scope.projectId, newText, function(result) {
				if (result.ok) {
					notice.push(notice.SUCCESS, newText.title + " settings successfully updated");
					$scope.textTitle = newText.title;
				}
			});
		}
	}])
	;

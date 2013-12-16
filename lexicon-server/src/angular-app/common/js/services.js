'use strict';

// Services
// ScriptureForge common services
angular.module('lf.services', ['jsonRpc'])
	.service('userService', ['jsonRpc', function(jsonRpc) {
		jsonRpc.connect('/api/lf'); // Note this doesn't actually 'connect', it simply sets the connection url.
		this.read = function(id, callback) {
			jsonRpc.call('user_read', [id], callback);
		};
		this.update = function(model, callback) {
			jsonRpc.call('user_update', [model], callback);
		};
		this.create = function(model, callback) {
			jsonRpc.call('user_create', [model], callback);
		};
		this.remove = function(userIds, callback) {
			jsonRpc.call('user_delete', [userIds], callback);
		};
		this.changePassword = function(id, password, callback) {
			jsonRpc.call('change_password', [id, password], callback);
		};
		this.list = function(callback) {
			// TODO Paging CP 2013-07
			jsonRpc.call('user_list', [], callback);
		};
		this.typeahead = function(term, callback) {
			jsonRpc.call('user_typeahead', [term], callback);
		};
		this.changePassword = function(userId, newPassword, callback) {
			jsonRpc.call('change_password', [userId, newPassword], callback);
		};
		this.usernameexists = function(userName, callback) {
			jsonRpc.call('username_exists', [userName], callback);
		};
	}])
	.service('languageService', ['jsonRpc', function(jsonRpc) {
		jsonRpc.connect('/api/lf'); // Note this doesn't actually 'connect', it simply sets the connection url.
		this.typeahead = function(term, callback) {
			jsonRpc.call('language_typeahead', [term], callback);
		};
	}])
	.service('projectService', ['jsonRpc', function(jsonRpc) {
		jsonRpc.connect('/api/lf'); // Note this doesn't actually 'connect', it simply sets the connection url.
		this.read = function(projectId, callback) {
			jsonRpc.call('project_read', [projectId], callback);
		};
		this.update = function(model, callback) {
			jsonRpc.call('project_update', [model], callback);
		};
		this.remove = function(projectIds, callback) {
			jsonRpc.call('project_delete', [projectIds], callback);
		};
		this.list = function(callback) {
			jsonRpc.call('project_list', [], callback);
		};
		this.readUser = function(projectId, userId, callback) {
			jsonRpc.call('project_readUser', [projectId, userId], callback);
		};
		this.updateUser = function(projectId, model, callback) {
			jsonRpc.call('project_updateUser', [projectId, model], callback);
		};
		this.removeUsers = function(projectId, users, callback) {
			jsonRpc.call('project_deleteUsers', [projectId, users], callback);
		};
		this.listUsers = function(projectId, callback) {
			// TODO Paging CP 2013-07
			jsonRpc.call('project_listUsers', [projectId], callback);
		};
	}])
	.service('textService', ['jsonRpc', function(jsonRpc) {
		jsonRpc.connect('/api/lf'); // Note this doesn't actually 'connect', it simply sets the connection url.
		this.read = function(projectId, textId, callback) {
			jsonRpc.call('text_read', [projectId, textId], callback);
		};
		this.update = function(projectId, model, callback) {
			jsonRpc.call('text_update', [projectId, model], callback);
		};
		this.remove = function(projectId, textIds, callback) {
			jsonRpc.call('text_delete', [projectId, textIds], callback);
		};
		this.list = function(projectId, callback) {
			jsonRpc.call('text_list_dto', [projectId], callback);
		};
		this.settings_dto = function(projectId, textId, callback) {
			jsonRpc.call('text_settings_dto', [projectId, textId], callback);
		};
	}])
	.service('questionsService', ['jsonRpc', function(jsonRpc) {
		jsonRpc.connect('/api/lf'); // Note this doesn't actually 'connect', it simply sets the connection url.
		this.read = function(projectId, questionId, callback) {
			jsonRpc.call('question_read', [projectId, questionId], callback);
		};
		this.update = function(projectId, model, callback) {
			jsonRpc.call('question_update', [projectId, model], callback);
		};
		this.remove = function(projectId, questionIds, callback) {
			jsonRpc.call('question_delete', [projectId, questionIds], callback);
		};
		this.list = function(projectId, textId, callback) {
			jsonRpc.call('question_list_dto', [projectId, textId], callback);
		};
	}])
	.service('questionService', ['jsonRpc', function(jsonRpc) {
		jsonRpc.connect('/api/lf');
		this.read = function(projectId, entryId, questionId, callback) {
			jsonRpc.call('question_comment_dto', [projectId, entryId, questionId], callback);
		};
		this.update = function(projectId, model, callback) {
			jsonRpc.call('question_update', [projectId, model], callback);
		};
		this.update_answer = function(projectId, questionId, model, callback) {
			jsonRpc.call('question_update_answer', [projectId, questionId, model], callback);
		};
		this.remove_answer = function(projectId, questionId, answerId, callback) {
			jsonRpc.call('question_remove_answer', [projectId, questionId, answerId], callback);
		};
		this.update_comment = function(projectId, questionId, answerId, model, callback) {
			jsonRpc.call('question_update_comment', [projectId, questionId, answerId, model], callback);
		};
		this.remove_comment = function(projectId, questionId, answerId, commentId, callback) {
			jsonRpc.call('question_remove_comment', [projectId, questionId, answerId, commentId], callback);
		};
		this.answer_voteUp = function(projectId, questionId, answerId, callback) {
			jsonRpc.call('answer_vote_up', [projectId, questionId, answerId], callback);
		};
		this.answer_voteDown = function(projectId, questionId, answerId, callback) {
			jsonRpc.call('answer_vote_down', [projectId, questionId, answerId], callback);
		};
	}])
	.service('questionTemplateService', ['jsonRpc', function(jsonRpc) {
		jsonRpc.connect('/api/lf');
		this.read = function(questionTemplateId, callback) {
			jsonRpc.call('questionTemplate_read', [questionTemplateId], callback);
		};
		this.update = function(questionTemplate, callback) {
			jsonRpc.call('questionTemplate_update', [questionTemplate], callback);
		};
		this.remove = function(questionTemplateIds, callback) {
			jsonRpc.call('questionTemplate_delete', [questionTemplateIds], callback);
		};
		this.list = function(callback) {
			jsonRpc.call('questionTemplate_list', [], callback);
		};
	}])
	.service('activityService', ['jsonRpc', function(jsonRpc) {
		jsonRpc.connect('/api/lf');
		this.list_activity = function(offset, count, callback) {
			jsonRpc.call('activity_list_dto', [offset, count], callback);
		};
	}])
	.service('sessionService', function() {
		this.currentUserId = function() {
			return window.session.userId;
		};
		
		this.realm = {
			SITE: function() { return window.session.userSiteRights; }
		};
		this.domain = {
				ANY:       function() { return 100;},
				USERS:     function() { return 110;},
				PROJECTS:  function() { return 120;},
				TEXTS:     function() { return 130;},
				QUESTIONS: function() { return 140;},
				ANSWERS:   function() { return 150;},
				COMMENTS:  function() { return 160;},
				TEMPLATES: function() { return 170;}
		};
		this.operation = {
				CREATE:       function() { return 1;},
				EDIT_OWN:     function() { return 2;},
				EDIT_OTHER:   function() { return 3;},
				DELETE_OWN:   function() { return 4;},
				DELETE_OTHER: function() { return 5;},
				LOCK:         function() { return 6;}
		};
		
		this.hasRight = function(rights, domain, operation) {
			var right = domain() + operation();
			return rights.indexOf(right) != -1;
		};
		
		this.session = function() {
			return window.session;
		};
	})
	.service('linkService', function() {
		this.href = function(url, text) {
			return '<a href="' + url + '">' + text + '</a>';
		};
		
		this.project = function(projectId) {
			return '/gwt/project/' + projectId;
			
		};
		
		this.projectSettings = function(projectId) {
			return '/app/projects#/project/' + projectId + '/settings';
			
		};
		this.text = function(projectId, textId) {
			return this.project(projectId) + "/" + textId;
		};
		
		this.question = function(projectId, textId, questionId) {
			return this.text(projectId, textId) + "/" + questionId;
		};
		
		this.user = function(userId) {
			return '/app/userprofile/' + userId;
		};
	})	
	.service('linkServiceGwt', function() {
		this.project = function(projectId) {
			return '/gwtangular/sfchecks#/project/' + projectId;
			
		};
		this.projectSettings = function(projectId) {
			return '/gwtangular/projects#/project/' + projectId + '/settings';
			
		};
		this.text = function(projectId, textId) {
			return this.project(projectId) + "/" + textId;
		};
		this.question = function(projectId, textId, questionId) {
			return this.text(projectId, textId) + "/" + questionId;
		};	
	})
	.service('lexEntryService', function() {
		var config = {
			'writingsystems': {
				'type': 'map',
				'map': {
					'th': 'Thai',
					'en': 'English',
					'my': 'Burmese'
				}
			},
			'entry': {
				'type': 'fields',
				'fields': ['lexeme', 'senses'],
				'definitions': {
					'lexeme': {
						'type': 'multitext',
						'label': 'Word',
						'writingsystems': ['th'],
						'width': 20
					},
					'senses': {
						'type': 'fields',
						'fields': ['definition', 'partOfSpeech', 'semanticDomainValue', 'examples'],
						'definitions': {
							'definition': {
								'type': 'multitext',
								'label': 'Meaning',
								'writingsystems': ['my', 'en'],
								'width': 20
							},
							'partOfSpeech': {
								'type': 'optionlist',
								'label': 'Part of Speech',
								'values': {
									'noun': 'Noun',
									'verb': 'Verb',
									'adjective': 'Adjective'
								},
								'width': 20
							},
							'semanticDomainValue': {
								'type': 'optionlist',
								'label': 'Semantic Domain',
								'values': {
									'2.1': '2.1 Body',
									'2.2': '2.2 Head and Shoulders',
									'2.3': '2.3 Feet'
								},
								'width': 20
							},
							'examples': {
								'type': 'fields',
								'fields': ['example', 'translation'],
								'definitions': {
									'example': {
										'type': 'multitext',
										'label': 'example',
										'writingsystems': ['th'],
										'width': 20
									},
									'translation': {
										'type': 'multitext',
										'label': 'translation',
										'writingsystems': ['en'],
										'width': 20
									}
								}
							}
						}
					}
				}
			}
		};
		var serverEntries = [];
		var dirtyEntries = [];
		var lastLocalId = 0;
		var lastServerId = 0;
		
		// for debugging
		this.serverEntries = function() {
			return serverEntries;
		};
		
		// for debugging
		this.dirtyEntries = function() {
			return dirtyEntries;
		};
		
		function serverIter(func) {
			for (var i=0; i<serverEntries.length; i++) {
				if (func(i, serverEntries[i])) {
					break;
				}
			}
		}
		
		// TODO: replace instances of this function with the full for loop
		// While this is a handy shortcut for developers, it makes the resulting code less readable
		// and perhaps less understandable.  Probably best to just code the full for loop everywhere
		// even though it is a few more keystrokes
		function dirtyIter(func) {
			for (var i=0; i<dirtyEntries.length; i++) {
				if (func(i, dirtyEntries[i])) {
					break;
				}
			}
		}
		
		function getNewServerId() {
			lastServerId++;
			return "server " + lastLocalId;
		}
		
		function getNewLocalId() {
			lastLocalId++;
			return "local " + lastLocalId;
		}
		
		// do we need to publish this to the service?
		this.getNewLocalId = function() {
			return getNewLocalId();
		};
		
		this.needToSave = function() {
			return dirtyEntries.length > 0;
		};
		
		this.saveNow = function(projectId, callback) {
			// save each entry in the dirty list
			dirtyIter(function(i, dirtyEntry) {
				// do update or add on server (server figures it out)
				var updated = false;
				serverIter(function(j, serverEntry) {
					if (serverEntry.id == dirtyEntry.id) {
						serverEntries[j] = dirtyEntry;
						updated = true;
						return true;
					}
				});
				if (!updated) {
					dirtyEntry.id = getNewServerId();
					serverEntries.unshift(dirtyEntry);
				}
			});
			dirtyEntries = [];
			callback({data:''});
		};

		this.read = function(projectId, id, callback) {
			var result = {};
			dirtyIter(function(i,e) {
				if (e.id == id) {
					result = e;
					return true;
				}
			});
			if (!result) {
				// read from server
				serverIter(function(i,e) {
					if (e.id == id) {
						result = e;
						return true;
					}
				});
			}
			callback({data: result});
			return;
		};
		
		this.update = function(projectId, entry, callback) {
			if (entry.hasOwnProperty('id') && entry.id != '') {
				var foundInDirty = false;
				dirtyIter(function(i,e) {
					if (e.id == entry.id) {
						dirtyEntries[i] = entry;
						foundInDirty = true;
						return true;
					}
				});
				if (!foundInDirty) {
					dirtyEntries.unshift(entry);
				}
			} else {
				entry.id = getNewLocalId();
				dirtyEntries.unshift(entry);
			}
			callback({data:entry});
		};
		
		this.remove = function(projectId, id, callback) {
			dirtyIter(function(i,e) {
				if (e.id == id) {
					dirtyEntries.splice(i, 1);
					return true;
				}
			});
			// remove from server
			serverIter(function(i,e){
				if (e.id == id) {
					serverEntries.splice(i, 1);
					return true;
				}
			}); 
			callback({data: {}});
			return;
		};
		
		this.getPageDto = function(projectId, callback) {
			var list = [];
			var ws = config.entry.definitions.lexeme.writingsystems[0];
			serverIter(function(i,e) {
				list.push({id: e.id, title: e.lexeme[ws]});
			});
			callback({data: { entries: list, config: config }});
		};
		
	})
	;

angular.module('palaso.ui.dc.example', ['palaso.ui.dc.multitext'])
  // Palaso UI Dictionary Control: Example Sentence
  .directive('dcExample', [function() {
		return {
			restrict : 'E',
			templateUrl : '/angular-app/common/directive/dc-example.html',
			scope : {
				config : "=",
				model : "=",
			},
			link : function(scope, element, attrs, controller) {
				if (!scope.model) {
					scope.model = {};
				}
			}
		};
  }])
  ;

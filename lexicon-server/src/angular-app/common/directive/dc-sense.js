angular.module('palaso.ui.dc.sense', ['palaso.ui.dc.multitext', 'palaso.ui.dc.optionlist', 'palaso.ui.dc.example'])
  // Palaso UI Dictionary Control: Sense
  .directive('dcSense', [function() {
		return {
			restrict : 'E',
			templateUrl : '/angular-app/common/directive/dc-sense.html',
			scope : {
				config : "=",
				model : "=",
			},
			controller: ['$scope', function($scope) {
				$scope.makeValidModel = function() {
					if (!$scope.model) {
						$scope.model = {};
					}
				};

				$scope.addExample = function() {
					$scope.model.examples.push({});
				};
				
				$scope.deleteExample = function(index) {
					if ($window.confirm("Are you sure you want to delete example #" + (index+1) + " ?")) {
						$scope.model.examples.splice(index, 1);
					}
				};
			}],
			link : function(scope, element, attrs, controller) {
				scope.$watch('model', function() {
					scope.makeValidModel();
				});
			}
		};
  }])
  ;

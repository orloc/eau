'use strict';

angular.module('eveTool')
.controller('corpListController', ['$scope', '$rootScope', 'selectedCorpManager', 'corporationDataManager', function($scope, $rootScope, selectedCorpManager, corporationDataManager){
    $scope.corps = [];
    $scope.selected_corp = null;
    $scope.needs_update = [];

    $scope.$watch('user_roles', function(data){
        if (typeof data === 'undefined'){
            return;
        }

        corporationDataManager.getAll().then(function(d) {
            $scope.corps = d;

            if($scope.corps.length === 1){
                $scope.selected_corp = $scope.corps[0];
            }

        }).then(function(){
            corporationDataManager.getNeedsUpdate().then(function(d){
                $scope.needs_update = d;
            });
        });

        $scope.getLabel = function(c){
            if (typeof c.corporation_details === 'undefined'){
                return 'Not Yet Updated';
            }

            return c.corporation_details.name;
        };

        $scope.$watch('selected_corp', function(value){
            if (typeof value === 'undefined' || value === null){
                return;

            }
            if (typeof value.corporation_details === 'undefined'){
                return;
            }

            selectedCorpManager.set(value);
        });

        $scope.selectCorp = function(c){
            $scope.selected_corp = c;
        };


        $scope.$on('update_list', function(event, item){
            $scope.needs_update.push(item);
        });
    });

}]);
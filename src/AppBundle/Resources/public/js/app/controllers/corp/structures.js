'use strict';

angular.module('eveTool')
    .controller('structureController', ['$scope', '$http', 'selectedCorpManager', function($scope, $http, selectedCorpManager){
        $scope.selected_corp = null;

        $scope.$watch(function(){ return selectedCorpManager.get(); }, function(val){
            if (typeof val.id === 'undefined'){
                return;
            }
            $scope.selected_corp = val;

            $http.get(Routing.generate('api.corporation.starbases', { id: val.id})).then(function(data){
                $scope.bases = data.data;
            });

        });

        $scope.hasAllianceAccess = function(settings){
            return  settings.allowAllianceMembers === '1';

        };

        $scope.hasCorpAccess = function(settings){
            return settings.allowCorporationMembers === '1';

        };

        $scope.resolveState = function(state){
            switch(state){
                case 0:
                    return 'Unanchored';
                case 1:
                    return 'Offline';
                case 2:
                    return 'Onlining';
                case 3:
                    return 'Reinforced';
                case 4:
                    return 'Online';
            }
        }
    }]);

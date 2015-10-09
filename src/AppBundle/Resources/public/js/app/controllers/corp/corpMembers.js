'use strict';

angular.module('eveTool')
    .controller('corpMembersController', ['$scope', '$http', 'selectedCorpManager', function($scope, $http, selectedCorpManager){
        $scope.selected_corp = null;
        $scope.$watch(function(){ return selectedCorpManager.get(); }, function(val) {
            if (typeof val === 'undefined' || typeof val.id === 'undefined') {
                return;
            }
            $scope.selected_corp = val;

            $http.get(Routing.generate('api.corporation.members', { id: val.id})).then(function(data){
                $scope.members = data.data;
            });
        });
    }]);

'use strict';

angular.module('eveTool')
    .controller('corpTitleController', ['$scope', '$http', 'selectedCorpManager', function($scope, $http, selectedCorpManager){
        $scope.selected_corp = null;
        $scope.image_width = 32;
        $scope.$watch(function(){ return selectedCorpManager.get(); }, function(val) {
            if (typeof val === 'undefined' || typeof val.id === 'undefined') {
                return;
            }
            $scope.selected_corp = val;

            $http.get(Routing.generate('api.titles', { id: val.id })).then(function(data){
                var filteredRoles = _.filter(data.data, function(d){
                    return d.title_name.length > 0;
                });
                $scope.titles = filteredRoles;
            });
        });
    }]);

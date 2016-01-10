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
        });
    }]);

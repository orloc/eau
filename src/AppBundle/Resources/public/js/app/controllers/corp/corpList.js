'use strict';

angular.module('eveTool')
.controller('corpListController', ['$scope', '$http', 'dataDispatcher', function($scope, $http, dataDispatcher){
    $scope.corps = [];
    $scope.selected_corp = null;

    $http.get(Routing.generate('api.corps')).then(function(data){
        $scope.corps = data.data;
    });

    $scope.selectCorporation = function(c){
        if ($scope.selected_corp === null || $scope.selected_corp.id !== c.id) {
            $scope.selected_corp = c;

            dataDispatcher.addEvent('select_corporation', c);
        }
    };

    $scope.$on('update_list', function(event, item){
        $scope.corps.push(item);
    });

}]);
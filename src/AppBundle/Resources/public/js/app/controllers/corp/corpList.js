'use strict';

angular.module('eveTool')
.controller('corpListController', ['$scope', '$http', 'dataDispatcher','selectedCorpManager', function($scope, $http, dataDispatcher, selectedCorpManager){
    $scope.corps = [];

    $http.get(Routing.generate('api.corps')).then(function(data){
        $scope.corps = data.data;
    });

    $scope.selectCorporation = function(c){
        if (c !== null){
            selectedCorpManager.set(c);
            $scope.selected_corp = c;
        }
    };

    $scope.$on('update_list', function(event, item){
        $scope.corps.push(item);
    });

}]);
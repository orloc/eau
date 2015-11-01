'use strict';

angular.module('eveTool')
.controller('corpListController', ['$scope', '$http', 'dataDispatcher','selectedCorpManager', function($scope, $http, dataDispatcher, selectedCorpManager){
    $scope.corps = [];
    $scope.needs_update = [];

    $http.get(Routing.generate('api.corps')).then(function(data){
        $scope.corps = data.data;
    });

    $http.get(Routing.generate('api.corp.needs_update')).then(function(data){
        $scope.needs_update = data.data;
    });

    $scope.getLabel = function(c){
        if (typeof c.corporation_details === 'undefined'){
            return 'Not Yet Updated';
        }

        return c.corporation_details.name;
    };

    $scope.$watch('selected_corp', function(value){
        if (typeof value === 'undefined'){
            return;

        }
        if (typeof value.corporation_details === 'undefined'){
            return;
        }

        selectedCorpManager.set(value);
    });


    $scope.$on('update_list', function(event, item){
        $scope.needs_update.push(item);
    });

}]);
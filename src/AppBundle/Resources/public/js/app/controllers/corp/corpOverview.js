'use strict';

angular.module('eveTool')
    .controller('corpOverviewController', ['$scope', '$http', function($scope, $http){
        $scope.selected_corp = null;

        $scope.$on('select_corporation', function(event, data){
            $scope.selected_corp = data;
        });

        $scope.$watch('selected_corp', function(val){
            console.log(val);
            if (val === null || val === undefined){
                return;
            }

            $http.get(Routing.generate('api.corporation.account', { id: val.id })).then(function(data){
                $scope.accounts = data.data;
            });

        });

    }]);

'use strict';

angular.module('eveTool')
    .controller('deliveryController', ['$scope', '$http', function($scope, $http){
        $scope.selected_corp = null;

        $scope.selected_region = null;

        $scope.$on('select_corporation', function(event, data){
            $scope.selected_corp = data;
        });

        $scope.$watch('selected_corp', function(val){
            if (val === null || typeof val === 'undefined'){
                return;
            }

            $http.get(Routing.generate('api.corporation.deliveries', { id: val.id})).then(function(data){
                console.log(data.data.items);
                $scope.assets = data.data.items;
            });

        });
    }]);

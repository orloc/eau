'use strict';

angular.module('eveTool')
    .controller('marketOrderController', ['$scope', '$http','selectedCorpManager', function($scope, $http, selectedCorpManager){
        $scope.orders = [];

        $scope.$watch(function(){ return selectedCorpManager.get(); }, function(val) {
            if (typeof val.id === 'undefined') {
                return;
            }

            $scope.selected_corp = val;
            $scope.loading = true;
            $scope.total_escrow = 0;
            $scope.total_sales = 0;
            $scope.orders = [];

            $http.get(Routing.generate('api.corporation.marketorders', { id: val.id})).then(function(data){
                return data.data;
            }).then(function(items){
                $scope.orders = items.items;
                $scope.total_escrow = items.total_escrow;
                $scope.total_sales = items.total_on_market;
                $scope.loading = false;
            });
        });

        $scope.percentFinished = function(item){
            return  100 - ((item.volume_remaining / item.total_volume)  * 100);
        };

    }]);

'use strict';

angular.module('eveTool')
    .controller('marketOrderController', ['$scope', '$http','$filter', function($scope, $http, $filter){
        $scope.selected_corp = null;
        $scope.loading = true;
        $scope.orders = [];

        $scope.$on('select_corporation', function(event, data){
            $scope.selected_corp = data;
            $scope.loading = true;
            $scope.total_escrow = 0;
            $scope.total_sales = 0;
            $scope.orders = [];
        });

        $scope.percentFinished = function(item){
            return  (item.volume_remaining / item.total_volume)   * 100;
        };

        $scope.$watch('selected_corp', function(val){
            if (val === null || typeof val === 'undefined'){
                return;
            }

            $http.get(Routing.generate('api.corporation.marketorders', { id: val.id})).then(function(data){
                return data.data;
            }).then(function(items){
                $scope.orders = items.items;
                $scope.total_escrow = items.total_escrow;
                $scope.total_sales = items.total_on_market;
                $scope.loading = false;

            });

        });

    }]);

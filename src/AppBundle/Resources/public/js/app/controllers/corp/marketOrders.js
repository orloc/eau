'use strict';

angular.module('eveTool')
    .controller('marketOrderController', ['$scope', 'corporationDataManager','selectedCorpManager', function($scope, corporationDataManager, selectedCorpManager){
        var orderCache = [];
        $scope.orders = [];
        $scope.image_width = 32;

        $scope.$watch(function(){ return selectedCorpManager.get(); }, function(val) {
            if (typeof val === 'undefined' || typeof val.id === 'undefined') {
                return;
            }

            $scope.selected_corp = val;
            $scope.loading = true;
            $scope.total_escrow = 0;
            $scope.total_sales = 0;
            $scope.orders = [];

            corporationDataManager.getMarketOrders(val).then(function(items){
                orderCache = items.items;
                $scope.orders = mapFillRatio(items.items);

                $scope.total_escrow = items.total_escrow;
                $scope.total_sales = items.total_on_market;
                $scope.loading = false;
            });

            corporationDataManager.getLastUpdate(val, 2).then(function(data){
                var data = data;

                $scope.updated_at = moment(data.created_at).format('x');
                $scope.update_succeeded = data.succeeded;
                $scope.next_update = moment(data.created_at).add(10, 'hours').format('x');
            });
        });

        var getOrdersByType = function (type){
            var newOrders = [];
            angular.forEach(orderCache, function (o){
                if (type === 'buy' && o.bid === true) {
                    newOrders.push(o);
                } else if (type === 'sell' && o.bid === false){
                    newOrders.push(o);
                }
            });

            return mapFillRatio(newOrders);
        };

        $scope.filterBuy = function(){
            $scope.orders = getOrdersByType('buy');
        };

        $scope.filterSell = function(){
            $scope.orders =  getOrdersByType('sell');
        };

        $scope.resetFilters = function(){
            $scope.orders = orderCache;

        };

        function mapFillRatio(items){
            var getFillRatio = function(item){
                return (item.volume_remaining / item.total_volume) * 100;
            };

            for(var i = 0; i <= items.length -1 ; i ++){
                items[i].ratio = getFillRatio(items[i]);
            }
            return items;
        }

        $scope.percentFinished = function(item){
            return  100 - ((item.volume_remaining / item.total_volume)  * 100);
        };

    }]);

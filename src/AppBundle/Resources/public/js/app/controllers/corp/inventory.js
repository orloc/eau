'use strict';

angular.module('eveTool')
    .controller('inventoryController', ['$scope', 'corporationDataManager', 'selectedCorpManager', function($scope, corporationDataManager, selectedCorpManager){
        $scope.loading = true;
        $scope.predicate = 'total_price';
        $scope.reverse = true;

        $scope.$watch(function(){ return selectedCorpManager.get(); }, function(val){
            if (typeof val.id === 'undefined'){
                return;
            }

            $scope.selected_corp = val;
            $scope.assets = [];
            $scope.loading = true;

            corporationDataManager.getCorpInventory(val).then(function(data){
                return data.items;
            }).then(function(items){
                $scope.assets = items.items;
                $scope.total_price = items.total_price;
                $scope.loading = false;
            });

            corporationDataManager.getLastUpdate(val, 2).then(function(data){
                $scope.updated_at = moment(data.created_at).format('x');
                $scope.update_succeeded = data.succeeded;
                $scope.next_update = moment(data.created_at).add(10, 'hours').format('x');
            });
        });

        $scope.totalM3 = function(){
            var total = 0;
            angular.forEach($scope.assets, function(item){
                total += $scope.getM3(item);
            });

            return total;
        };


        $scope.getM3 = function(item){
            if (item && typeof item.descriptors != 'undefined' && typeof item.descriptors.volume !== 'undefined')
                return parseFloat(item.descriptors.volume) * item.quantity;
        };

        $scope.sumItems = function(){
            if (!$scope.price_reference.length){
                return 0;
            }

            var total = 0;
            angular.forEach($scope.assets, function(item){
                var price = $scope.getPrice(item);

                if (typeof price != 'undefined'){
                    total += price.average_price * item.quantity;
                }
            });

            return total;

        };


        $scope.getPrice = function(type){
            if (typeof type === 'undefined'){
                return;
            }

            return type.descriptors.total_price;

        };

        $scope.order = function(predicate){
            $scope.reverse = ($scope.predicate === predicate) ? !$scope.reverse : false;
            $scope.predicate = predicate;
        };

    }]);

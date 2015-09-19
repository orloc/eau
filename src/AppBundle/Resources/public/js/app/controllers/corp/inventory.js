'use strict';

angular.module('eveTool')
    .controller('inventoryController', ['$scope', '$http', '$q', 'selectedCorpManager', function($scope, $http, $q, selectedCorpManager){
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

            $http.get(Routing.generate('api.corporation.assets', { id: val.id})).then(function(data){
                return data.data.items;
            }).then(function(items){
                $scope.assets = items.items;
                $scope.total_price = items.total_price;
                $scope.loading = false;
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

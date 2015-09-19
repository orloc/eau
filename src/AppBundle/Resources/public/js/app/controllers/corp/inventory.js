'use strict';

angular.module('eveTool')
    .controller('inventoryController', ['$scope', '$http', '$q', function($scope, $http, $q){
        $scope.selected_corp = null;
        $scope.loading = true;
        $scope.predicate = 'total_price';
        $scope.reverse = true;

        $scope.$on('select_corporation', function(event, data){
            $scope.selected_corp = data;
            $scope.assets = [];
            $scope.loading = true;
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

        $scope.$watch('selected_corp', function(val){
            if (val === null || typeof val === 'undefined'){
                return;
            }

            $scope.loading = true;

            $http.get(Routing.generate('api.corporation.assets', { id: val.id})).then(function(data){
                return data.data.items;
            }).then(function(items){
                $scope.assets = items.items;
                $scope.total_price = items.total_price;
                $scope.loading = false;
            });

        });

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

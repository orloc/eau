'use strict';

angular.module('eveTool')
    .controller('inventoryController', ['$scope', '$http', function($scope, $http){
        $scope.selected_corp = null;
        $scope.loading = true;
        $scope.price_reference = [];

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

        $scope.tryAccess = function (item, key){
        }

        $scope.$watch('selected_corp', function(val){
            if (val === null || typeof val === 'undefined'){
                return;
            }

            $http.get(Routing.generate('api.corporation.assets', { id: val.id})).then(function(data){
                return data.data.items;
            }).then(function(items){

                var ids = _.pluck(items, 'type_id').unique();

                $http.get(Routing.generate('api.price.averagelist', { typeId: ids })).then(function(data){
                    $scope.price_reference = data.data;
                    $scope.assets = items;

                    $scope.loading = false;
                });
            });

        });

        $scope.getPrice = function(type){
            if (typeof type === 'undefined'){
                return;
            }

            var price = _.find($scope.price_reference, function(p){
                return parseInt(p.type_id) === parseInt(type.type_id);
            });

            return price;

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

        }
    }]);

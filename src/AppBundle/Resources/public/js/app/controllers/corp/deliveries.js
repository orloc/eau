'use strict';

angular.module('eveTool')
    .controller('deliveryController', ['$scope', '$http', function($scope, $http){
        $scope.selected_corp = null;
        $scope.selected_region = null;
        $scope.price_reference = [];
        $scope.assets = [];

        $scope.$on('select_corporation', function(event, data){
            $scope.selected_corp = data;
            $scope.price_reference = [];
            $scope.assets = [];
        });

        $scope.$watch('selected_corp', function(val){
            if (val === null || typeof val === 'undefined'){
                return;
            }

            $http.get(Routing.generate('api.corporation.deliveries', { id: val.id})).then(function(data){
                return data.data.items;
            }).then(function(items){
                var ids = _.pluck(items, 'type_id');

                $http.get(Routing.generate('api.price.averagelist', { typeId: ids })).then(function(data){
                    $scope.price_reference = data.data;

                    $scope.assets = items;

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

        $scope.sumDeliveries = function(){
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

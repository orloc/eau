'use strict';

angular.module('eveTool')
    .controller('deliveryController', ['$scope', '$http','$filter', function($scope, $http, $filter){
        $scope.selected_corp = null;
        $scope.selected_region = null;
        $scope.loading = true;
        $scope.price_reference = [];
        $scope.assets = [];
        $scope.filter = '*';
        $scope.order_by_value = 'quantity';
        $scope.order_by_reverse = true;

        $scope.filtered_assets = [];

        $scope.$on('select_corporation', function(event, data){
            $scope.selected_corp = data;
            $scope.price_reference = [];
            $scope.loading = true;
            $scope.assets = [];
            $scope.filtered_assets = [];
        });

        $scope.$watch('selected_corp', function(val){
            if (val === null || typeof val === 'undefined'){
                return;
            }

            $http.get(Routing.generate('api.corporation.deliveries', { id: val.id})).then(function(data){
                return data.data.items;
            }).then(function(items){
                var ids = _.pluck(items, 'type_id').unique();

                $http.get(Routing.generate('api.price.averagelist', { typeId: ids })).then(function(data){
                    $scope.price_reference = data.data;
                    $scope.assets = items;

                    $scope.filtered_assets = $scope.filterBy("*");
                    $scope.loading = false;
                });

            });

        });

        $scope.getRowClass = function(item){
            if (typeof item.total_m3 !== 'undefined'){

                var tm3 = item.total_m3;
                if (tm3 >= 100000 && tm3 <= 249999){
                    return -1;
                }

                if (tm3 >= 250000 && tm3 <= 499999){
                    return 0;
                }

                if (tm3 >= 500000 && tm3 <= 799999){
                    return 1;
                }

                if (tm3 >= 800000){
                    return 2;
                }
            }
        };

        $scope.filterBy = function(){
            $scope.loading = true;
            if ($scope.filter == "*"){
                return $scope.assets;
            }

            var tmp = [];

            var flattenMap = function(temp){
                var newTmp = _.values(temp);
                var tmpV = [];

                angular.forEach(newTmp, function(v){
                    angular.forEach(v, function(item){
                        item.total_m3 = 0;
                        item.actual_total = 0;
                    });
                });

                angular.forEach(newTmp, function(v){
                    tmpV.push(_.reduce(v, function(total, value){

                        if (typeof total === 'undefined'){
                            return value;
                        }
                        var m3 = $scope.getM3(value);


                        if (typeof total.actual_total == 'undefined') {
                            total.actual_total = 0;
                        }

                        if (typeof total.total_m3 == 'undefined'){
                            total.total_m3 = 0;
                        }

                        total.actual_total += value.descriptors.total_price;
                        total.total_m3 += m3;

                        return total;
                    }));
                });

                return tmpV;
            };

            switch ($scope.filter){
                case 'region':
                    angular.forEach($scope.assets, function(a){
                        if (typeof(tmp[a.descriptors.region]) != 'object'){
                            tmp[a.descriptors.region] = [];
                        }

                        tmp[a.descriptors.region].push(a);

                    });

                    return flattenMap(tmp);

                case 'constellation':
                    angular.forEach($scope.assets, function(a){
                        if (typeof(tmp[a.descriptors.constellation]) != 'object'){
                            tmp[a.descriptors.constellation] = [];
                        }

                        tmp[a.descriptors.constellation].push(a);
                    });

                    return flattenMap(tmp);

                case 'system':
                    angular.forEach($scope.assets, function(a){
                        if (typeof(tmp[a.descriptors.solar_system]) != 'object'){
                            tmp[a.descriptors.solar_system] = [];
                        }

                        tmp[a.descriptors.solar_system].push(a);
                    });

                    return flattenMap(tmp);


                case 'station':
                    angular.forEach($scope.assets, function(a){
                        if (typeof(tmp[a.descriptors.station]) != 'object'){
                            tmp[a.descriptors.station] = [];
                        }

                        tmp[a.descriptors.station].push(a);
                    });

                    return flattenMap(tmp);
            }
        };

        $scope.getShowColumn = function(column){
            var f = $scope.filter;

            switch(column){
                case 'constellation':
                    return f != 'region';
                case 'system':
                    return f != 'region' && f != 'constellation';
                case 'station':
                    return f != 'region' && f != 'constellation' && f != 'system';
            }

            return true;

        };

        $scope.doFilter = function(search){
            $scope.filter = search;
            $scope.filtered_assets = $scope.filterBy(search);
            $scope.loading = false;
        };

        $scope.totalM3 = function(){
            var total = 0;
            angular.forEach($scope.assets, function(item){
                total += $scope.getM3(item);
            });

            return total;
        };

        $scope.getM3 = function(item){
            if (item)
            return parseFloat(item.descriptors.volume) * item.quantity;
        };

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

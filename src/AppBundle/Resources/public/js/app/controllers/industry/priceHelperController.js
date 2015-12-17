'use strict';

angular.module('eveTool')
    .controller('priceHelperController', ['$scope', '$http','corporationDataManager', '$filter', function($scope, $http, corporationDataManager, $filter){
        $scope.selected_items = [];
        $scope.selected_price_profiles = [];
        $scope.selected_corporation = null;

        corporationDataManager.getAll().then(function(d){
            $scope.corporations = d;
        });
        $http.get(Routing.generate('api.item_list')).then(function(d){
            $scope.items = d.data;
            $scope.show_items = false;
        });

        $http.get(Routing.generate('api.price_regions')).then(function(d){
            $scope.regions = d.data;
        });

        $scope.$watch('selected_items', function(i){
            if (i.length === 0){
                return;
            }
            updateView();
        });

        $scope.$watch('selected_price_profiles', function(i){
            if (i.length === 0){
                return;
            }

            updateView();
        });

        $scope.getLastTrans = function(i, type){
            var name = "last_"+type;
            if (typeof i[name] === 'undefined' || i[name].length <= 0){
                return false;
            }
            return i[name][0];
        };

        $scope.inflatePrice = function(price, increase){
            return (parseFloat(price) * (parseFloat(increase) / 100)) + parseFloat(price);
        };

        $scope.getRegionPrices = function(i, r){
            if (typeof i[r] !== 'undefined'){
                return i[r][i.item.typeID];
            }
        };

        $scope.getRegionObjects = function(){
            var newList = [];

            angular.forEach($scope.selected_price_profiles, function(pp){
                newList.push( _.find($scope.regions, function(R){
                    return R.regionID  === pp;
                }));
            });
            return newList;
        };

        function updateView (){
            if ($scope.selected_items.length > 0 && $scope.selected_price_profiles.length >0){
                $http.post(Routing.generate('api.price_lookup', {id:$scope.selected_corporation }), { regions: $scope.selected_price_profiles, items: $scope.selected_items }).then(function(data){
                    $scope.item_result = data.data;
                });
            }
            return;
        }

    }]);

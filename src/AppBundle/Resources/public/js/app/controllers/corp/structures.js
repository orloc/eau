'use strict';
angular.module('eveTool')
    .controller('structureController', ['$scope', 'corporationDataManager', 'selectedCorpManager', function($scope, corporationDataManager, selectedCorpManager){
        $scope.loading = true;
        $scope.getTowerFuelQuantities = function(fuel, tower){
            if (typeof fuel !== 'undefined' && typeof tower !== 'undefined'){
                var fuelVolume = parseInt(fuel.quantity) * parseFloat(fuel.type.volume);
                var actualSize =  fuel.typeID === "16275" ? 50000 :140000;
                var percentage = (function(tower){
                    actualSize = resolveTowerSize(tower, actualSize);
                    return ((fuelVolume/parseFloat(actualSize)) * 100).toPrecision(2);
                })(tower);

                return { 'percentage': percentage, 'max': actualSize, 'actual': fuel.quantity };
            }
        };

        $scope.getTowerCost = function(tower){
            var actualSize = 40;
            var consumption = resolveTowerSize(tower, actualSize) * 24;
            var block = _.find(tower.descriptors.fuel, function(f){
                return f.typeID !== "16275";
            });

            return consumption * block.price;
        };

        var getTimeToOffline = function(tower){
            var date = moment(),
                fuel = _.find(tower.fuel, function(f){
                return f.typeID !== "16275";
            });

            var perCycle = parseInt(tower.descriptors.fuel_consumption.quantity);
            var secStatus = parseFloat(tower.descriptors.security) <= 0;
            if (secStatus){
                perCycle = perCycle - (perCycle * 0.25) ;
            }
            var remaining = parseInt(fuel.quantity) / perCycle;
            date.add(remaining, 'hours');
            return parseFloat(date.format('x'));
        };

        $scope.selected_corp = null;
        $scope.image = {
            width: 16
        };

        $scope.$watch(function(){ return selectedCorpManager.get(); }, function(val){
            if (typeof val.id === 'undefined'){
                return;
            }
            $scope.selected_corp = val;

            corporationDataManager.getStructures(val).then(function(data){
                $scope.bases = data;
                angular.forEach($scope.bases, function(b, k){
                    $scope.bases[k].timeToOffline = getTimeToOffline(b);
                });
                $scope.loading = false;
            });
        });


        $scope.hasAllianceAccess = function(settings){
            return  settings.allowAllianceMembers === '1';
        };

        $scope.hasCorpAccess = function(settings){
            return settings.allowCorporationMembers === '1';
        };

        $scope.resolveState = function(state){
            switch(state){
                case 0:
                    return 'Unanchored';
                case 1:
                    return 'Offline';
                case 2:
                    return 'Onlining';
                case 3:
                    return 'Reinforced';
                case 4:
                    return 'Online';
            }
        }

        function resolveTowerSize(tower, scale){
            var size = _.find(tower.descriptors.attributes, function(d){
                return d.attributeID === '1031';
            }).valueInt;

            if (typeof size !== 'undefined'){
                switch(parseInt(size)){
                    case 3:
                        break;
                    case 2:
                        scale = scale  / 2;
                        break;
                    case 1:
                        scale = scale / 4;
                        break;
                }
            }

            return scale;
        }

    }]);

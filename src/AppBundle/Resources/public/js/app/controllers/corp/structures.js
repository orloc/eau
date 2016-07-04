'use strict';
angular.module('eveTool')
    .controller('structureController', ['$scope', 'corporationDataManager', 'selectedCorpManager', function($scope, corporationDataManager, selectedCorpManager){
        function getTimeToOffline(tower){
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

        
        var tower_list = [];
        $scope.loading = true;
        $scope.bases = [];
        $scope.selected_corp = null;
        $scope.image = {
            width: 16
        };
        
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

        $scope.$watch(function(){ return selectedCorpManager.get(); }, function(val){
            if (typeof val.id === 'undefined'){
                return;
            }
            $scope.selected_corp = val;

            corporationDataManager.getStructures(val)
                .then(function(data) {
                    return _.map(data, function(b){
                        try {
                            b.timeToOffline = getTimeToOffline(b);
                        } catch (e){
                            b.timeToOffline = 0;
                            console.log(e);
                        }
                        return b;
                    });
                })
                .then(function(data){
                    tower_list = data;
                    $scope.bases = _.filter(tower_list, function(t) {
                        return t.state === 4;
                    });
                    $scope.loading = false;
                });
        });
        
        $scope.showTowers = function(state){
            switch (state){
                case 'all':
                    $scope.bases = tower_list;
                    break;
                case 'off':
                    $scope.bases = _.filter(tower_list, function(t) {
                        return t.state === 1;
                    });
                    break;
                case 'on':
                    $scope.bases = _.filter(tower_list, function(t) {
                        return t.state === 4;
                    });
                    break;
                default: break;
            }
        };

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
        };

    }]);

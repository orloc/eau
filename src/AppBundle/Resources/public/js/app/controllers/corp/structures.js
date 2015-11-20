'use strict';

angular.module('eveTool')
    .controller('structureController', ['$scope', '$http', 'selectedCorpManager', function($scope, $http, selectedCorpManager){

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

        $scope.getTowerFuelQuantities = function(fuel, tower){
            if (typeof fuel !== 'undefined' && typeof tower !== 'undefined'){
                var fuelVolume = parseInt(fuel.quantity) * parseFloat(fuel.type.volume);

                var actualSize =  fuel.typeID === "16275" ? 50000 :140000;

                var percentage = (function(tower){
                    actualSize = resolveTowerSize(tower, actualSize);

                    return ((fuelVolume/parseFloat(actualSize)) * 100).toPrecision(2);

                })(tower);

                return { 'percentage': percentage, 'max': actualSize, 'actual': fuelVolume };
            }
        };

        $scope.getTowerCost = function(tower){
            var actualSize = 40;

            // blocks per  hour
            var consumption = resolveTowerSize(tower, actualSize) * 24;

            //find the blocks
            var block = _.find(tower.descriptors.fuel, function(f){
                return f.typeID !== "16275";
            });

            return consumption * block.price;
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

            $http.get(Routing.generate('api.corporation.starbases', { id: val.id})).then(function(data){
                $scope.bases = data.data;
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
    }]);

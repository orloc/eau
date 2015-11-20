'use strict';

angular.module('eveTool')
    .controller('structureController', ['$scope', '$http', 'selectedCorpManager', function($scope, $http, selectedCorpManager){

        $scope.getTowerFuelQuantities = function(fuel, tower){
            if (typeof fuel !== 'undefined' && typeof tower !== 'undefined'){
                var fuelVolume = parseInt(fuel.quantity) * parseFloat(fuel.type.volume);

                return (function(tower){
                    var size = _.find(tower.descriptors.attributes, function(d){
                        return d.attributeID === '1031';

                    }).valueInt;

                    if (typeof size !== 'undefined'){
                        var actualSize = 140000;
                        switch(parseInt(size)){
                            case 1:
                                break;
                            case 2:
                                actualSize = actualSize  / 2;
                                break;
                            case 3:
                                actualSize = actualSize / 4;
                                break;

                        }

                        return ((fuelVolume/parseFloat(actualSize)) * 100).toPrecision(2);
                    }

                    return 0;
                })(tower);
            }
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

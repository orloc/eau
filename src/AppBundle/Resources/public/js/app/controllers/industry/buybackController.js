'use strict';

angular.module('eveTool')
    .controller('buybackController', ['$scope', '$http', function($scope, $http){
        $scope.input_data = '';
        $scope.updated_items = [];
        $scope.errors = [];
        $scope.loading = false;


        $scope.submit = function(){
            $scope.loading = true;
            var inputs = $scope.input_data.split("\n");

            console.log(inputs);
            var r = /\d{1,3}(,\d{3})*(\.\d+)?(?=\s)/;
            var data = [];

            angular.forEach(inputs, function(i){
                i.trim();

                if (i.length > 0){
                    var res = r.exec(i);
                    var name = i.substr(0, res.index).trim();

                    if (name.length > 0){
                        var datum = {
                            name: name,
                            quantity: res[0].trim()
                        };

                        if (datum.quantity.length >= 4){
                            datum.quantity = datum.quantity.replace(',','');
                        }

                        var exists = _.find(data, function(i){
                            return i.name === datum.name;
                        });

                        if (typeof exists !== 'undefined'){
                            exists.quantity = parseInt(exists.quantity);
                            exists.quantity += parseInt(datum.quantity);
                        } else {
                            data.push(datum);
                        }
                    } else {
                        $scope.errors.push(i);
                    }
                }
            });

            var callback = function(extra){
                return function(data){
                    var items = data.data;
                    angular.forEach(items, function(i){
                        var qData = _.find(extra, function(d){
                            return d.name == i.typeName;
                        });

                        i['quantity'] = parseInt(qData.quantity);
                        i['total_value'] = parseFloat(i['quantity'] * i.price);
                    });

                    $scope.updated_items = items;

                    $scope.total_value  =  _.reduce($scope.updated_items, function(total, value){
                        if (typeof total === 'undefined'){
                            return value;
                        }

                        total.total_value += value.total_value;

                        return total;
                    });
                };
            };

            $http.post(Routing.generate('api.buyback_items'), data).then(callback(data)).then(function(){

                $scope.loading = false;
            });
        };
    }]);

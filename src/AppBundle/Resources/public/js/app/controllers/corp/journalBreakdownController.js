'use strict';

angular.module('eveTool')
    .controller('journalBreakdownController', ['$scope', '$http', '$q', 'selectedCorpManager', function($scope, $http, $q, selectedCorpManager){
        $scope.selected_account = null;

        $scope.member_segments = [];

        $scope.$watch(function(){ return selectedCorpManager.get(); }, function(val){
            if (typeof val.id === 'undefined'){
                return;
            }

            $scope.loading = true;
            $scope.selected_corp = val;

            update(val);
        });

        $scope.$watch('current_date', function(){
            update($scope.selected_corp);
        });

        $scope.sumTransactions = function(){
            return _.reduce(_.pluck($scope.ref_types, 'total'), function(init, carry){
                return init + carry;
            });
        };

        $scope.getSegments = function(list, size){
            return _.chunk(list, size);
        };

        function update(val){
            var date = moment($scope.current_date).format('X');

            if (typeof val !== 'undefined'){
                $http.get(Routing.generate('api.corporation.journal.aggregate', { id: val.id, date: date })).then(function(data) {
                    $scope.ref_types = data.data;

                    angular.forEach($scope.ref_types, function(ref, i){
                        var sum = _.reduce(_.pluck(ref.trans, 'amount'), function(init, carry){
                            return init + carry;
                        });

                        $scope.ref_types[i]['total'] = sum;
                    });

                    $scope.segments = $scope.getSegments($scope.ref_types, ($scope.ref_types.length / 2) + 1 );
                });

                $http.get(Routing.generate('api.corporation.journal.user_aggregate', { id: val.id, date: date })).then(function(data) {
                    $scope.members = data.data;

                    angular.forEach($scope.members, function(ref, i){
                        var sum = _.reduce(_.pluck(ref.trans, 'amount'), function(init, carry){
                            return init + carry;
                        });

                        $scope.members[i]['total'] = sum;
                    });

                    $scope.member_segments = $scope.getSegments($scope.members, ($scope.members.length / 2) + 1 );
                });
            }
        }
    }]);

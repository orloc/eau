'use strict';

angular.module('eveTool')
    .controller('journalBreakdownController', ['$scope', 'corporationDataManager', 'selectedCorpManager', function($scope, corporationDataManager, selectedCorpManager){
        $scope.selected_account = null;
        $scope.member_segments = [];

        $scope.$watch(function(){ return selectedCorpManager.get(); }, function(val){
            if (typeof val.id === 'undefined'){
                return;
            }

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
            $scope.loading = true;
            var date = moment($scope.current_date).format('X');

            if (typeof val !== 'undefined'){
                corporationDataManager.getJournalTypeAggregate(val, date).then(function(data){
                    $scope.ref_types = data;

                    angular.forEach($scope.ref_types, function(ref, i){
                        var sum = _.reduce(_.pluck(ref.trans, 'amount'), function(init, carry){
                            return init + carry;
                        });

                        $scope.ref_types[i]['total'] = sum;
                    });

                    $scope.segments = $scope.getSegments($scope.ref_types, ($scope.ref_types.length / 2) + 1 );

                });

                corporationDataManager.getJournalUserAggregate(val,date).then(function(data) {
                    $scope.members = data;

                    angular.forEach($scope.members, function(ref, i){
                        var sum = _.reduce(_.pluck(ref.trans, 'amount'), function(init, carry){
                            return init + carry;
                        });

                        $scope.members[i]['total'] = sum;
                    });

                    $scope.member_segments = $scope.getSegments($scope.members, ($scope.members.length / 2) + 1 );
                    $scope.loading = false;
                });
            }
        }
    }]);

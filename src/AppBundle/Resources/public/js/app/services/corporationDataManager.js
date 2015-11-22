'use strict';

angular.module('eveTool')
    .service('corporationDataManager', [ '$q', '$http', function($q, $http){

        var deferredStack = {};

        var loading = false;

        function getDeferred(route){

            var deferred = $q.defer();
            if (typeof deferredStack[route] !== 'undefined'){
                deferredStack[route].reject();
            } else {
                deferredStack[route] = deferred;
            }

            loading = true;

            $http.get(route)
                .success(function(data){
                    loading = false;
                    deferred.resolve(data);
                })
                .error(function(err){
                    loading = false;
                    deferred.reject(err);
                });

            return deferred.promise;
        }

        return {
            isLoading: function(){
                return loading;
            },
            getAll: function () {
                return getDeferred(Routing.generate('api.corps'));
            },
            getNeedsUpdate: function () {
                return getDeferred(Routing.generate('api.corp.needs_update'));

            },
            getMarketTransactions: function (corp, account, date, type) {
                return getDeferred(Routing.generate('api.corporation.account.markettransactions', {
                    id: corp.id,
                    acc_id: account.id,
                    date: date,
                    type: type
                }));
            },
            getJournalTransactions: function (corp, account, date) {
                return getDeferred(Routing.generate('api.corporation.account.journaltransactions', {
                    id: corp.id,
                    acc_id: account.id,
                    date: date
                }));
            },
            getAccounts: function (corp, date) {
                return getDeferred(Routing.generate('api.corporation.account', {
                    id: corp.id,
                    date: date
                }));

            },
            getLastUpdate: function (corp, type) {
                return getDeferred(Routing.generate('api.corporation.apiupdate', {
                    id: corp.id,
                    type: type
                }));
            },
            getJournalTypeAggregate: function (corp, date) {
                return getDeferred(Routing.generate('api.corporation.journal.aggregate', {id: corp.id, date: date}));

            },
            getJournalUserAggregate: function (corp, date) {
                return getDeferred(Routing.generate('api.corporation.journal.user_aggregate', {
                    id: corp.id,
                    date: date
                }));

            }
        };
 }]);

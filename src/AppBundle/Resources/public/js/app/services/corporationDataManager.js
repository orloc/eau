'use strict';

angular.module('eveTool')
    .service('corporationDataManager', [ '$q', '$http', function($q, $http){

        var deferredStack = {};

        function getDeferred(route){

            var deferred = $q.defer();
            if (typeof deferredStack[route] !== 'undefined'){
                console.log(deferredStack[route]);
                deferredStack[route].reject();
            } else {
                deferredStack[route] = deferred;
            }

            $http.get(route)
                .success(function(data){
                    deferred.resolve(data);
                })
                .error(function(err){
                    deferred.reject(err);
                });

            return deferred.promise;
        }

        return {
            getAll: function(){
                return getDeferred(Routing.generate('api.corps'));
            },
            getNeedsUpdate: function(){
                return getDeferred(Routing.generate('api.corp.needs_update'));

            },
            getMarketTransactions: function(corp, account, date, type){
                return getDeferred(Routing.generate('api.corporation.account.markettransactions', {
                    id: corp.id,
                    acc_id: account.id,
                    date: date,
                    type: type
                }));
            },
            getJournalTransactions: function(corp, account, date){
                return getDeferred(Routing.generate('api.corporation.account.journaltransactions', {
                    id: corp.id,
                    acc_id: account.id,
                    date: date
                }));
            },
            getAccounts: function(corp, date){
                return getDeferred(Routing.generate('api.corporation.account', {
                    id: corp.id,
                    date: date
                }));

            },
            getLastUpdate: function(corp, type){
                return getDeferred(Routing.generate('api.corporation.apiupdate', {
                    id: corp.id,
                    type: type
                }));
            }
        }

    }]);

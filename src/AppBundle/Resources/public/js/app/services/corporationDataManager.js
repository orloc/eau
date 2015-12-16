'use strict';

angular.module('eveTool')
    .service('corporationDataManager', [ '$q', '$http', function($q, $http){

        var deferredStack = {};

        function getDeferred(route){

            var deferred = $q.defer();
            if (typeof deferredStack[route] !== 'undefined' && deferredStack[route] !== null){
                deferredStack[route].resolve();
            }

            deferredStack[route] = deferred;

            $http.get(route, { timeout: deferredStack[route] })
                .success(function(data){
                    deferred.resolve(data);
                    deferredStack[route] = null;
                })
                .error(function(err){
                    deferred.reject(err.message);
                    if (err.code === 403){
                      //  window.location.replace(Routing.generate('eve.login.redirect',{}, true));
                    }
                });

            return deferred.promise;
        }

        return {
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
            },
            getStructures: function(corp){
                return getDeferred(Routing.generate('api.corporation.starbases', { id: corp.id}));
            },
            getCorpInventory: function(corp, page, per_page){
                return getDeferred(Routing.generate('api.corporation.assets', { id: corp.id, page: page, per_page: per_page }));
            },
            getCorpInventorySorted: function(corp, sort){
                return getDeferred(Routing.generate('api.corporation.assets.clustered', { id: corp.id, sort: sort }));
            },
            getCorpDeliveries: function(corp){
                return getDeferred(Routing.generate('api.corporation.deliveries', { id: corp.id}));
            },
            getMarketGroups: function(){
                return getDeferred(Routing.generate('api.market_groups'));
            }

        };
 }]);

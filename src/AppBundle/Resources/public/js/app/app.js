'use strict';

angular.module('eveTool', [
    'ngMessages',
    'angular-ladda',
    'ngCookies',
    'ngAnimate',
    'angular-spinkit',
    'timer',
    'ui.bootstrap',
    'angularSlideables',
    'localytics.directives',
    'angular-storage',
    'angular-jwt'
])
.config(['$httpProvider', 'jwtInterceptorProvider', function ($httpProvider, jwtInterceptorProvider){
    jwtInterceptorProvider.tokenGetter = ['config', 'store', function(config, store){
        const token = store.get('jwt');
        return token ? token.token : token;
    }];

    $httpProvider.interceptors.push('jwtInterceptor');
}])
.run(['$http', '$rootScope', 'store', function($http, $rootScope, store){
    if (typeof token !== 'undefined'){
        store.set('jwt', { token: token });
    }
    $http.get(Routing.generate('api.auth')).then(function(data){
        $rootScope.user_roles = data.data.roles;
    });
}]);


Array.prototype.unique = function(){
    var o = {}, i, l = this.length, r = [];
    for(i=0; i<l;i+=1) o[this[i]] = this[i];
    for(i in o) r.push(o[i]);
    return r;
};


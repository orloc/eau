'use strict';

angular.module('eveTool', [
    'ngMessages',
    'angular-ladda',
    'ngAnimate',
    'angular-spinkit',
    'ui.bootstrap',
    'angularSlideables',
    'localytics.directives'
]).run(['$http', '$rootScope', function($http, $rootScope){
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


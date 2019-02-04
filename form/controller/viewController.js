var app = angular.module('cryptoApp', []);

app.controller('viewController', function($scope, $http) {

    var handler = window.location.href.split('/form')[0];

    $scope.init = function(){
        $scope.caricaDati();
    };

    $scope.caricaDati = function(){

        $http.post(handler + '/form/controller/viewHandler.php',
            {'function': 'getDatiPagina'}
        ).then(function (data) {
            //console.log(data.data);
            $scope.user = data.data.user;
        })
    };

    $scope.showCard = function (user) {
        $scope.creditCard = null;
        $scope.id = user.id;
        $scope.userSelected = user.name;
    };

    $scope.insertKey = function (key) {

        $http.post(handler + '/form/controller/viewHandler.php',
            {'function': 'showData', 'id': $scope.id, 'key': key}
        ).then(function (data) {
            $scope.key = '';
            if(data.data.status === 'KO'){
                swal(data.data.message, '', 'error');
            }else{
                $scope.creditCard = data.data.creditCard;
            }
        })
    };

    $scope.goToRegistrationForm = function () {
        window.location.href = handler + '/form/registration.html';
    };

});
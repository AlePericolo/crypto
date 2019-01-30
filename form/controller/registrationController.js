var app = angular.module('cryptoApp', []);

app.controller('registrationController', function($scope, $http) {

    var handler = window.location.href.split("/form")[0];

    $scope.caricamentoCompletato = false;
    $scope.dataCorrect = false;

    $scope.init = function(){
        $scope.caricaDati();
    };

    /*========================================== CARICO DATI PAGINA ==================================================*/

    $scope.caricaDati = function(){

        $http.post(handler + '/form/controller/registrationHandler.php',
            {'function': 'getDatiPagina'}
        ).then(function (data) {
            console.log(data.data);
            $scope.user = data.data.user;
            $scope.card = data.data.card;
            $scope.cardType = data.data.cardType;
            $scope.months = data.data.months;
            $scope.years = data.data.years;

        }).then(function () {
            $scope.caricamentoCompletato = true;
        });
    };

    $scope.$watchGroup(['user.name', 'card.type', 'card.number', 'card.cvv', 'card.expirationMonth', 'card.expirationYear'],
        function (){
            if ($scope.user && $scope.card) {
                if(
                    $scope.user.name !== '' &&
                    $scope.card.type !== '' &&
                    $scope.card.number !== '' &&
                    $scope.card.cvv !== '' &&
                    $scope.card.expirationMonth !== '' &&
                    $scope.card.expirationYear !== ''
                ){
                    $scope.dataCorrect = true;
                }else{
                    $scope.dataCorrect = false;
                }
            }
        }
    );

    $scope.checkCreditCardFormat = function () {


        //var number = $scope.card.number;
        //var type = $scope.card.type;

        var number = 340000000000009;
        var type = 'American Express';

        console.log(number);
        console.log(type);

        if (checkCreditCard(number, type)) {
            alert("Credit card has a valid format")
        } else {
            console.log(ccErrors);
            alert(ccErrors[ccErrorNo])
        }

    }


});
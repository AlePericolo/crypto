var app = angular.module('cryptoApp', []);

app.controller('registrationController', function($scope, $http) {

    var handler = window.location.href.split('/form')[0];

    $scope.dataCorrect = false;

    $scope.init = function(){
        $scope.caricaDati();
    };

    $scope.caricaDati = function(){

        $http.post(handler + '/form/controller/registrationHandler.php',
            {'function': 'getDatiPagina'}
        ).then(function (data) {
            $scope.user = data.data.user;
            $scope.card = data.data.card;
            $scope.cardType = data.data.cardType;
            $scope.months = data.data.months;
            $scope.years = data.data.years;
        })
    };

    $scope.$watchGroup(['user.name', 'card.holder', 'card.type', 'card.number', 'card.cvv', 'card.expirationMonth', 'card.expirationYear'],
        function (){
            if ($scope.card) {
                if(
                    $scope.user.name !== '' &&
                    $scope.card.holder !== '' &&
                    $scope.card.type !== '' &&
                    $scope.checkCreditCard() &&
                    $scope.checkDate()
                ){
                    $scope.dataCorrect = true;
                }else{
                    $scope.dataCorrect = false;
                }
            }
            //console.log('Correct: ' + $scope.dataCorrect);
        }
    );

    $scope.checkCreditCard = function () {

        var visaRegEx = /^(?:4[0-9]{12}(?:[0-9]{3})?)$/;
        var mastercardRegEx = /^(?:5[1-5][0-9]{14})$/;
        var amexpRegEx = /^(?:3[47][0-9]{13})$/;
        var cvv3RegEx = /^[0-9]{3}$/;
        var cvv34RegEx = /^[0-9]{3,4}$/;
        var isValid = false;

        switch($scope.card.type) {
            case 'American Express':
                //console.log('American Express');
                if(amexpRegEx.test($scope.card.number) && cvv34RegEx.test($scope.card.cvv)){
                    isValid = true;
                }
                break;
            case 'MasterCard':
                //console.log('MasterCard');
                if(mastercardRegEx.test($scope.card.number) && cvv3RegEx.test($scope.card.cvv)){
                    isValid = true;
                }
                break;
            case 'Visa':
                //console.log('Visa');
                if(visaRegEx.test($scope.card.number) && cvv3RegEx.test($scope.card.cvv)){
                    isValid = true;
                }
                break;
            default:
                isValid = false
        }
        //console.log('Number + cvv: ' + isValid);
        return isValid;
    };

    $scope.checkDate = function () {

        var isValid = false;
        if($scope.card.expirationMonth !== '' && $scope.card.expirationYear !== ''){
            if ($scope.card.expirationYear > (new Date()).getFullYear()){
                isValid = true;
            }else{
                if($scope.card.expirationMonth >= (new Date()).getMonth() + 1){
                    isValid = true;
                }
            }
        }
        //console.log('Date: ' + isValid);
        return isValid;
    };


    $scope.save = function () {
        $scope.user.text = 'holder_' + $scope.card.holder + ';card_' + $scope.card.type + ';number_' + $scope.card.number + ';cvv_' + $scope.card.cvv + ';expiration_' + $scope.card.expirationMonth + '/' + $scope.card.expirationYear;
        console.log($scope.user);

        $http.post(handler + '/form/controller/registrationHandler.php',
            {'function': 'save', 'user': $scope.user}
        ).then(function (data) {
            if(data.data.response === 'OK'){
                swal(data.data.message, '', 'success');
            }else{
                swal(data.data.message, '', 'error');
            }
        }).then(function () {
            window.location.reload();
        })
    };

    $scope.goToViewData = function () {
        window.location.href = handler + '/form/view.html';
    };

});
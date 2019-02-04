<?php
/**
 * Created by PhpStorm.
 * User: alessandro
 * Date: 26/09/18
 * Time: 15.01
 */

require_once '../../conf/conf.php';
require_once '../../src/lib/pdo.php';
require_once '../../src/lib/functions.php';

require_once '../../src/model/User.php';
require_once '../../src/model/Crypt.php';

function getDatiPagina($request){

    $result = array();

    $pdo = connettiPdo();
    $user = new User($pdo);

    $result['user'] = $user->getEmptyKeyArray();
    $result['card'] = array('type'=>'','number'=>'','cvv'=>'','expirationMonth'=>'','expirationYear'=>'');
    $result['cardType'] = ['American Express', 'MasterCard', 'Visa'];
    $result['months'] = ['01','02','03','04','05','06','07','08','09','10','11','12'];
    $yars = [];
    for($i = date("Y"); $i <= (date("Y") + 10); $i ++){
        array_push($yars,$i."");
    }
    $result['years'] = $yars;

    return json_encode($result);
}


function save($request){

    $result = array();

    $pdo = connettiPdo();

    try{
        $pdo->beginTransaction();
        $user = new User($pdo);
        $user->setName($request->user->name);
        $crypt = new Crypt();
        $user->setText($crypt->cryptData($request->user->text));
        $user->saveOrUpdate();
        $pdo->commit();
        $result['response'] = 'OK';
        $result['message'] = 'Data saved successfully';
    }catch (PDOException $e){
        $pdo->rollBack();
        $result['response'] = 'KO';
        $result['message'] = $e->getMessage();
    }
    return json_encode($result);
}

//create/read session
ob_start();
session_start();
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$function = $request->function;
$r = $function($request);
echo $r;
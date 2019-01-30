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

function getDatiPagina($request){

    $result = array();

    $pdo = connettiPdo();
    $user = new User($pdo);

    $result['user'] = $user->getEmptyKeyArray();
    $result['card'] = array('type'=>'','number'=>'','cvv'=>'','expirationMonth'=>'','expirationYear'=>'');
    $result['cardType'] = ['American Express', 'Carte Blanche', 'Discover', 'Diners Club', 'enRoute', 'JCB', 'MasterCard', 'Solo', 'Switch', 'Visa', 'Laser'];
    $result['months'] = ['01','02','03','04','05','06','07','08','09','10','11','12'];
    $yars = [];
    for($i = date("Y"); $i <= (date("Y") + 10); $i ++){
        array_push($yars,$i."");
    }
    $result['years'] = $yars;

    return json_encode($result);
}


function salvaDati($request){

    $result = array();

    $pdo = connettiPdo();

    try{
        $pdo->beginTransaction();
        $post = new Post($pdo);
        $post->findByPk($request->post->id);
        $post->setTesto($request->post->testo);
        $post->saveOrUpdate();
        $pdo->commit();
        $result['response'] = 'OK';
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
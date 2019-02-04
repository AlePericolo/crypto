<?php
/**
 * Created by PhpStorm.
 * User: alessandro
 * Date: 31/01/19
 * Time: 14.30
 */

require_once '../../conf/conf.php';
require_once '../../src/lib/pdo.php';
require_once '../../src/lib/functions.php';

require_once '../../src/model/User.php';
require_once '../../src/model/Decrypt.php';

function getDatiPagina($request){

    $result = array();

    $pdo = connettiPdo();
    $user = new User($pdo);
    $result['user'] = $user->findUser(User::FETCH_KEYARRAY);

    return json_encode($result);
}

function showData($request){

    $result = array();

    $pdo = connettiPdo();
    $text = User::getTextByIdStatic($pdo, $request->id);

    $decrypt = new Decrypt();
    $decryptResult = $decrypt->decryptData($text, $request->key);
    if($decryptResult){
        $ccData = (explode(";",$decryptResult));
        foreach ($ccData as $c){
            list($k, $v) = explode('_', $c);
            $result['creditCard'][ $k ] = $v;
        }
        return json_encode($result);
    }else{
        return json_encode(array("status"=>"KO", "message"=>"Invalid key"));
    }
}

//create/read session
ob_start();
session_start();
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$function = $request->function;
$r = $function($request);
echo $r;
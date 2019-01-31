<?php
/**
 * Created by PhpStorm.
 * User: alessandro
 * Date: 31/01/19
 * Time: 12.26
 */

class Crypt
{
    function cryptData($data){

        //error_log('current dir '. getcwd());

        $myfilePublic = fopen("../../key/public_key.pem", "r") or die("Unable to open file!");
        $publickey = fread($myfilePublic,filesize("../../key/public_key.pem"));
        fclose($myfilePublic);

        openssl_public_encrypt($data, $cryptdata, $publickey);

        $base64Crypted = base64_encode($cryptdata);

        return $base64Crypted;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: alessandro
 * Date: 31/01/19
 * Time: 14.34
 */

class Decrypt
{
    function decryptData($data, $key){

        $base64Decrypted = base64_decode($data);
        if (openssl_private_decrypt($base64Decrypted, $decrypted, $key)){
            return $decrypted;
        }else{
            return false;
        }
    }
}
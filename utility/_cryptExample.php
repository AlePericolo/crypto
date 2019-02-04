<?php
/**
 * Created by PhpStorm.
 * User: alessandro
 * Date: 30/01/19
 * Time: 10.47
 */

if (isset($_SERVER['HTTPS']) )
{
    echo "SECURE: This page is being accessed through a secure connection.<br><br>";
}
else
{
    echo "UNSECURE: This page is being access through an unsecure connection.<br><br>";
}
/*
// Create the keypair
$res=openssl_pkey_new();

// Get private key
openssl_pkey_export($res, $privatekey);

// Get public key
$publickey=openssl_pkey_get_details($res);
$publickey=$publickey["key"];
*/

$myfilePrivate = fopen("key/private_key.pem", "r") or die("Unable to open file!");
$privatekey = fread($myfilePrivate,filesize("key/private_key.pem"));
fclose($myfilePrivate);

$myfilePublic = fopen("key/public_key.pem", "r") or die("Unable to open file!");
$publickey = fread($myfilePublic,filesize("key/public_key.pem"));
fclose($myfilePublic);

echo "Private Key:<BR>$privatekey<br><br>Public Key:<BR>$publickey<BR><BR>";

$cleartext = '1234567890123456';

echo "Clear text:<br>$cleartext<BR><BR>";

openssl_public_encrypt($cleartext, $crypttext, $publickey);

echo "Crypt text:<br><code>$crypttext</code><BR><BR>";

$baseCrypted = base64_encode($crypttext);

echo "$baseCrypted"."<BR><BR>";

//echo "lunghezza ".strlen($baseCrypted)."<BR><BR>";

$baseDecrypted = base64_decode($baseCrypted);

openssl_private_decrypt($baseDecrypted, $decrypted, $privatekey);

echo "Decrypted text:<BR>$decrypted<br><br>";

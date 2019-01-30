<?php
/**
* Developed by: Alessandro Pericolo
* Date: 30/01/2019
* Time: 15:02
* Version: 0.1
**/

require_once 'UserModel.php';

class User extends UserModel {

/*CONSTRUCTOR*/
function __construct(PDO $pdo){
	parent::__construct($pdo);
}

} //close Class User
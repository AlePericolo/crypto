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

public function findUser($typeResult=self::FETCH_OBJ){
    $query = 'select id, name from user';
    return $this->createResultArray($query, null,$typeResult);
}

public function getTextById($id){
    $query = 'select text from user where id = ?';
    return $this->createResultValue($query,array($id));
}

public static function getTextByIdStatic($pdo, $id){
    $app = new self($pdo);
    return $app->getTextById($id);
}

} //close Class User
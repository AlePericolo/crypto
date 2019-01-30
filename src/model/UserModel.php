<?php
/**
* Developed by: Alessandro Pericolo
* Date: 30/01/2019
* Time: 15:02
* Version: 0.1
**/

require_once 'AbstractModel.php';

class UserModel extends AbstractModel {

/** @var integer PrimaryKey */
protected $id;
/** @var string */
protected $name;
/** @var string */
protected $text;

/* CONSTRUCTOR ------------------------------------------------------------------------------------------------------ */

//constructor
function __construct($pdo){
	parent::__construct($pdo);
	$this->tableName = "user";
}

/* FUNCTIONS -------------------------------------------------------------------------------------------------------- */

/** 
* find by PrimaryKey: 
* @return User|array|string|null
**/
public function findByPk($id, $typeResult = self::FETCH_OBJ){
	$query = "SELECT * FROM $this->tableName USE INDEX(PRIMARY) WHERE ID=?";
	return $this->createResult($query, array($id), $typeResult);
}

/** 
* delete by PrimaryKey: 
**/
public function deleteByPk($id){
	$query = "DELETE FROM $this->tableName WHERE ID=?";
	return $this->createResultValue($query, array($id));
}

/** 
* find all record of table 
* @return User[]|array|string
**/
public function findAll($distinct = false, $typeResult = self::FETCH_OBJ, $limit = -1, $offset = -1){
	$distinctStr = ($distinct) ? "DISTINCT" : "";
	$query = "SELECT $distinctStr * FROM $this->tableName ";
	if ($this->whereBase) $query .= " WHERE $this->whereBase";
	if ($this->orderBase) $query .= " ORDER BY $this->orderBase";
	$query .= $this->createLimitQuery($limit, $offset);
	return $this->createResultArray($query, null, $typeResult);
}

/** 
* trasform the Object into a KeyArray 
* @return array
**/
public function createKeyArray(){
	$keyArray = array();
	if (isset($this->id)) $keyArray["id"] = $this->id;
	if (isset($this->name)) $keyArray["name"] = $this->name;
	if (isset($this->text)) $keyArray["text"] = $this->text;
	return $keyArray;
}

/** 
* trasform the KeyArray into a Object 
* @param array $keyArray
**/
public function createObjKeyArray(array $keyArray){
	if (isset($keyArray["id"])) $this->id = $keyArray["id"];
	if (isset($keyArray["name"])) $this->name = $keyArray["name"];
	if (isset($keyArray["text"])) $this->text = $keyArray["text"];
}

/** 
* return the Object as an empty KeyArray 
* @return array
**/
public function getEmptyKeyArray(){
	$emptyKeyArray = array();
	$emptyKeyArray["id"] = "";
	$emptyKeyArray["name"] = "";
	$emptyKeyArray["text"] = "";
	return $emptyKeyArray;
}

/** 
* return columns' list as string 
* @return string
**/
public function getListColumns(){
	return "id, name, text";
}

/* CREATE TABLE ----------------------------------------------------------------------------------------------------- */

/** 
* DDL create table query 
**/
public function createTable(){
return $this->pdo->exec(
"CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `text` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1"
);
}

/* GETTER & SETTER -------------------------------------------------------------------------------------------------- */

/** 
* @return integer
**/
public function getId(){
	 return $this->id;
}

/** 
* @param integer $id
**/
public function setId($id){
	 $this->id = $id;
}

/** 
* @return string
**/
public function getName(){
	 return $this->name;
}

/** 
* @param string $name
* @param int $encodeType
 **/
public function setName($name, $encodeType = self::STR_DEFAULT){
	 $this->name = $this->decodeString($name, $encodeType);
}

/** 
* @return string
**/
public function getText(){
	 return $this->text;
}

/** 
* @param string $text
* @param int $encodeType
 **/
public function setText($text, $encodeType = self::STR_DEFAULT){
	 $this->text = $this->decodeString($text, $encodeType);
}


} //close Class UserModel
<?php
/**
 * Created by PhpStorm.
 * User: alessandro
 * Date: 18/04/18
 * Time: 12.15
 */

/**
 * Funzione di connessione a PDO. La funzione consente di specificare il database a cui collegarsi.
 * Oltre questo, permette il passaggio dell'array indicizzato per i dati di connessione forzati composti da:
 * PWD - Password
 * USER - Username
 * SERVER - Indirizzo del server
 * ATTRIBUTE - attributi di connessione
 * CHARSET - Charset da utilizzare.
 * @param string|null $dbName
 * @param array|null
 * @return bool|PDO
 */
function connettiPdo($dbName = null, $server = null){

    //error_log("CONNETTI PDO");

    if (is_null($dbName)) $dbName = DBNAME;
    $attribute = array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
    if (is_null($server)) $server = SERVER;
    $user = DBUSER;
    $password = DBPASSWORD;
    $charset = DBCHARSET;

    $dsn = 'mysql:dbname='.$dbName.';host='.$server.';charset='.$charset;

    try{
        $pdo = new PDO($dsn, $user, $password, $attribute);
    }catch (PDOException $e){
        error_log('ERRORE CONNESSIONE A DB: ' .$e->getMessage());
        die;
    }
    return $pdo;
}

function findSchemas(){

    //error_log("FIND SCHEMAS");

    $attribute = array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
    $server = SERVER;
    $user = DBUSER;
    $password = DBPASSWORD;
    $charset = DBCHARSET;

    $dsn = 'mysql:dbname=mysql;host='.$server.';charset='.$charset;

    //try{
        $pdo = new PDO($dsn, $user, $password, $attribute);

        $ambienti = array();

        $statement = $pdo->query("SHOW SCHEMAS");
        while($row = $statement->fetch(PDO::FETCH_COLUMN)){
            $ambienti[] = $row;
        }

        return $ambienti;

    //}catch (PDOException $e){
       // error_log('ERRORE CONNESSIONE A DB FIND SCHEMAS: ' .$e->getMessage());
        die;
    //}
}

function findServer(){
    return explode(',',SERVERS);
}

/**
 * @param $connessione PDO
 *
 */
function disconnettiPdo(&$connessione){
    $connessione=null;
}

/**
 * @param $pdo PDO
 * @param null $errore
 * @param int $codice
 */
function bloccaPdo($pdo,$errore=null,$codice=0){
    if ((isIstance($pdo)) and $pdo->inTransaction()) errorePdo($errore,$codice);
    return;
}

/**
 * Funzione per l'esecuzione delle query tramite prepare/execute
 * @param PDO|null $pdo classe di gestione database
 * @param string|PDOStatement $stringPrepared la stringa preparativa. Sostituire le variabili con ?
 * @param array $arrayElementi l'array mono/bidimensionale contenente i valori
 * (NOTA: per riga, DEVONO coincidere coi punti interrogativi!!!)
 * @param char|string $chrTipoRitorno variabile 'classica' (p=PDOStatement [default], b=blob, f=fetch/array, v=valore)
 * @param int $tipoFetch tipo di ritorno dato richiesto
 * @return PDOStatement|array|string ritorna il valore, uguale alla forma di queryPdo
 * ove $boolMatrice fosse true, ne sarebbe un'array di tali elementi.
 */
function queryPreparedPdo($pdo,$stringPrepared,$arrayElementi=null,$chrTipoRitorno='p',$tipoFetch=null){
    $boolConnettiLocale=(!($pdo instanceof PDO));
    if ($boolConnettiLocale) {
        $pdo=connettiPdo();
    }
    if (!$pdo instanceof PDO) {
        phpToAlert('Attezione! Connesione fallita!');
        die;
    }
    if (!$stringPrepared ){
        bloccaPdo($pdo);
    }
    /** @var $queryHandler PDOStatement */
    $queryHandler=($stringPrepared instanceof PDOStatement)?$stringPrepared:$pdo->prepare($stringPrepared);
    if (!is_null($arrayElementi) and !is_array($arrayElementi)){
        $arrayElementi = array($arrayElementi);
    }

    $queryHandler->execute($arrayElementi);
    if ('p' == strtolower($chrTipoRitorno)){
        return $queryHandler;
    }
    $fetching = (is_null($tipoFetch))?PDO::FETCH_ASSOC:$tipoFetch;
    switch (strtolower($chrTipoRitorno)){
        case 'b':
            $ritorno=$queryHandler->fetchAll($fetching);
            break;
        case 'f':
            $ritorno=$queryHandler->fetch($fetching);
            break;
        case 'v':
            $fetching = (is_null($tipoFetch))?0:$tipoFetch;
            $ritorno=$queryHandler->fetchColumn($fetching);
            break;
    }
    $queryHandler->closeCursor();
    if ($boolConnettiLocale) {
        $pdo=null;
    }
    return $ritorno;
}
/**
 * Semplice richiamo all'errore PDO
 * @param null $strMessaggio
 * @param int $intCodice
 * @throws PDOException
 */
function errorePdo($strMessaggio=null,$intCodice=0){
    throw new PDOException($strMessaggio,$intCodice);
}


/**
 * Funzione per effettuare l'INSERT di un array $array[<campoDB>]=>valore.
 * Nota: ogni valore giunto null verra' eliminato.
 * @param PDO $pdo
 * @param string $strTabella
 * @param array $arrayKey
 * @param bool $boolMatrice
 *
 * @return null|string
 */
function insertKeyArrayPdo($pdo,$strTabella,$arrayKey,$boolMatrice=false){

    /* FixMe: rimosso debug di test di Claudio
     * $debugTrace = debug_backtrace();
    debug_print_backtrace();
    */

    if ( !boolArrayAssociativo($arrayKey) or !is_array($arrayKey) or !$arrayKey or $boolMatrice) return null;
    if (!$strTabella){
        bloccaPdo($pdo,'Errore grave. La tabella non e\' arrivata.');
        return 'Errore grave. La tabella non e\' arrivata.';
    }
    $boolConnetti = (!$pdo);
    if ($boolConnetti){
        $pdo=connettiPdo();
    }
    $arrayCampi=array();
    $intNumRigheMax=0;
    $arrayPosizioni=array();
    foreach($arrayKey as $key=>$value) {
        if (null===$value or ($boolMatrice and !$value)) {
            unset($arrayKey[$key]);
            continue;
        }
        $arrayCampi[]=$key;
        $arrayPosizioni[]=':'.$key;
    }
    if (!$arrayCampi) return null;
    if ($boolMatrice){
        foreach ($arrayKey as $value) if ($intNumRigheMax < count($value))$intNumRigheMax = count($value);

        foreach($arrayKey as $key => $value){
            if ($intNumRigheMax==count($value)) continue;
            for ($i=count($value);$i<$intNumRigheMax;$i++) $arrayKey[$key][$i]='';
        }
    }
    $insert=$pdo->prepare('INSERT INTO '.$strTabella.' ('.implode(',',$arrayCampi).') VALUES ('.implode(',',$arrayPosizioni).')');
    if ($boolMatrice):
        $arrayTemp=array();
        for ($i=0;$i<$intNumRigheMax;$i++):
            foreach($arrayKey as $key => $valore) $arrayTemp[$key]=(null===$valore[$i])?'':$valore[$i];
            $insert->execute($arrayTemp);
        endfor;
    else:
        $insert->execute($arrayKey);
    endif;
    $ritorno=$pdo->lastInsertId();
    if ($boolConnetti){
        $pdo = null;
    }
    return $ritorno;

}
/**
 * @param  PDO   $pdo
 * @param string  $strTabella
 * @param array  $arrayKey
 * @param mixed  $idValue
 * @param string $strIdColumn
 * @return null|string|int
 */
function updateKeyArrayPdo($pdo,$strTabella,$arrayKey,$idValue,$strIdColumn='ID'){
    $boolConnetti = (!$pdo);

    if ($boolConnetti){
        $pdo = connettiPdo();
    }
    if (!$strIdColumn or !$idValue  or !$strTabella){
        bloccaPdo($pdo,'Errore grave. L\'ID della riga, la colonna di riferimento o la tabella non sono arrivati.');
        return 'Errore grave. L\'ID della riga, la colonna di riferimento o la tabella non sono arrivati.';
    }
    $arrayCampi=array();
    foreach($arrayKey as $chiave => $valore) {
        if (null===$valore) {
            unset($arrayKey[$chiave]);
            continue;
        }
        $arrayCampi[]=$chiave.'=:'.$chiave;
    }
    if (!$arrayCampi) return;
    $update=$pdo->prepare('UPDATE '.$strTabella.' SET '.implode(',',$arrayCampi).' WHERE '.$strIdColumn.'=:ID__COLUMN');
    $arrayKey['ID__COLUMN']=$idValue;
    $update->execute($arrayKey);
    $nrecord = $update->rowCount();
    if ($boolConnetti){
        $pdo = null;
    }
    return $nrecord;
}



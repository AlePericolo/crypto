<?php
date_default_timezone_set('Europe/Rome');
//require_once 'config.php';
//require_once 'pdo.php';
//header("Content-type: text/html; charset=latin1");
/**
 * La funzione legge la data passata e la converte in formato SQL: formati ammessi: AAAA-MM-GG, GG/MM/AAAA, GG/MM/AA
 *
 * @param string $data
 *
 * @return bool|string
 */
function registraData($data = 'now')
{
    if ('now' == strtolower($data)) {
        return trasformaData();
    }
    $dataItaliana = trasformaData($data, false);
    if ($dataItaliana) {
        return $data;
    }
    $dataMySQL = trasformaData($data);
    if (!$dataMySQL) {
        return false;
    }
    if (trasformaData($dataMySQL, false, 'Y') < 100) {
        $dataMySQL = trasformaData($data, true, 'd/m/y');
    }
    if (trasformaData($dataMySQL, false, 'Y') < 1000) {
        return false;
    }

    return $dataMySQL;
}

function daCodHTMLaJS($stringa, $boolStampa = false)
{
    $arrayElementiHTML = array(
        '&Agrave;' => array("\xC0", '%C0'),
        '&Aacute;' => array("\xC1", '%C1'),
        '&Acirc;'  => array("\xC2", '%C2'),
        '&Atilde;' => array("\xC3", '%C3'),
        '&Auml;'   => array("\xC4", '%C4'),
        '&Aring;'  => array("\xC5", '%C5'),
        '&AElig;'  => array("\xC6", '%C6'),
        '&Ccedil;' => array("\xC7", '%C7'),
        '&Egrave;' => array("\xC8", '%C8'),
        '&Eacute;' => array("\xC9", '%C9'),
        '&Ecirc;'  => array("\xCA", '%CA'),
        '&Euml;'   => array("\xCB", '%CB'),
        '&Igrave;' => array("\xCC", '%CC'),
        '&Iacute;' => array("\xCD", '%CD'),
        '&Icirc;'  => array("\xCE", '%CE'),
        '&Iuml;'   => array("\xCF", '%CF'),
        '&ETH;'    => array("\xD0", '%D0'),
        '&Ntilde;' => array("\xD1", '%D1'),
        '&Ograve;' => array("\xD2", '%D2'),
        '&Oacute;' => array("\xD3", '%D3'),
        '&Ocirc;'  => array("\xD4", '%D4'),
        '&Otilde;' => array("\xD5", '%D5'),
        '&Ouml;'   => array("\xD6", '%D6'),
        '&Oslash;' => array("\xD8", '%D8'),
        '&Ugrave;' => array("\xD9", '%D9'),
        '&Uacute;' => array("\xDA", '%DA'),
        '&Ucirc;'  => array("\xDB", '%DB'),
        '&Uuml;'   => array("\xDC", '%DC'),
        '&Yacute;' => array("\xDD", '%DD'),
        '&THORN;'  => array("\xDE", '%DE'),
        '&szlig;'  => array("\xDF", '%DF'),
        '&agrave;' => array("\xE0", '%E0'),
        '&aacute;' => array("\xE1", '%E1'),
        '&acirc;'  => array("\xE2", '%E2'),
        '&atilde;' => array("\xE3", '%E3'),
        '&auml;'   => array("\xE4", '%E4'),
        '&aring;'  => array("\xE5", '%E5'),
        '&aelig;'  => array("\xE6", '%E6'),
        '&ccedil;' => array("\xE7", '%E7'),
        '&egrave;' => array("\xE8", '%E8'),
        '&eacute;' => array("\xE9", '%E9'),
        '&ecirc;'  => array("\xEA", '%EA'),
        '&euml;'   => array("\xEB", '%EB'),
        '&igrave;' => array("\xEC", '%EC'),
        '&iacute;' => array("\xED", '%ED'),
        '&icirc;'  => array("\xEE", '%EE'),
        '&iuml;'   => array("\xEF", '%EF'),
        '&eth;'    => array("\xF0", '%F0'),
        '&ntilde;' => array("\xF1", '%F1'),
        '&ograve;' => array("\xF2", '%F2'),
        '&oacute;' => array("\xF3", '%F3'),
        '&ocirc;'  => array("\xF4", '%F4'),
        '&otilde;' => array("\xF5", '%F5'),
        '&ouml;'   => array("\xF6", '%F6'),
        '&oslash;' => array("\xF8", '%F8'),
        '&ugrave;' => array("\xF9", '%F9'),
        '&uacute;' => array("\xFA", '%FA'),
        '&ucirc;'  => array("\xFB", '%FB'),
        '&uuml;'   => array("\xFC", '%FC'),
        '&yacute;' => array("\xFD", '%FD'),
        '&thorn;'  => array("\xFE", '%FE'),
        '&yuml;'   => array("\xFF", '%FF'),
        '&OElig;'  => array("\u0152", '%u0152'),
        '&oelig;'  => array("\u0153", '%u0153'),
        '&Scaron;' => array("\u0160", '%u0160'),
        '&scaron;' => array("\u0161", '%u0161'),
        '&Yuml;'   => array("\u0178", '%u0178'),
        '&fnof;'   => array("\u0192", '%u0192'),
        '&euro;'   => array("\u20AC", chr(128)),
    );

    foreach ($arrayElementiHTML as $html => $codifica) {
        if ($boolStampa) {
            $stringa = str_replace($html, $codifica[1], $stringa);
        }
        else {
            $stringa = str_replace($html, $codifica[0], $stringa);
        }

    }
    $carattere = ($boolStampa) ? '' : PHP_EOL;
    $stringa = str_ireplace(array('<br>', '<br />', '<br/>'), $carattere, $stringa);

    return $stringa;
}

function datoDaPHPaJS($valore)
{
    if (!isset($valore) or is_array($valore)) {
        if (is_null($valore)) {
            return 'null';
        }

        return 'undefined';
    }

    if (true === $valore) {
        return 'true';
    }
    if (false === $valore) {
        return 'false';
    }
    if (is_string($valore)) {
        $valore = str_replace('\'', '\\\'', $valore);

        return '\'' . $valore . '\'';
    }

    return $valore;
}

/**
 * @param      $nomeArray
 * @param      $arrayValori
 * @param bool $boolForzaArray
 *
 * @return string
 */
function arrayPHPtoJS($nomeArray, $arrayValori, $boolForzaArray = true)
{
    $strRitorno = '';
    if (!$nomeArray) {
        return $strRitorno;
    }
    if (!is_array($arrayValori) and !$boolForzaArray) {
        return $nomeArray . ' = ' . datoDaPHPaJS($arrayValori) . ';';
    }
    if (!is_array($arrayValori)) {
        $arrayValori = array($arrayValori);
    }
    $strRitorno = $nomeArray . ' = [';
    $arrayTemp = array();
    foreach ($arrayValori as $valore) {
        $arrayTemp[] = datoDaPHPaJS($valore);
    }
    $strRitorno .= implode(',', $arrayTemp);
    $strRitorno .= '];';

    return $strRitorno;

}

class TrasformaData extends DateTime
{
    /**
     * Forza la data ad essere valida
     *
     * @param boolean $boolForce
     */
    public function forzaDataCorretta($boolForce)
    {
        $this->boolForce = $boolForce;
        $this->inizializzaData();
    }

    /**
     * @return boolean
     */
    public function getBoolForce()
    {
        return $this->boolForce;
    }

    /**
     * Inverte i parametri passati
     *
     * @param boolean $boolToSave
     */
    public function settaInvertiFormati($boolToSave)
    {
        $this->boolToSave = $boolToSave;
        $this->inizializzaData();
    }

    /**
     * @return boolean
     */
    public function getBoolToSave()
    {
        return $this->boolToSave;
    }

    /**
     * Sovrascrive il valore della data con un oggetto DateTime
     *
     * @param \DateTime $objDate
     */
    public function settaOggettoData($objDate)
    {
        $this->objDate = $objDate;
    }

    /**
     * @return \DateTime
     */
    public function getObjDate()
    {
        return $this->objDate;
    }

    /**
     * Forza la data di ingresso
     *
     * @param string $strData
     * @param string $strData1
     * @param string $strData2
     */
    public function settaData($strData, $strData1 = null, $strData2 = null)
    {
        $this->strData = $strData;
        if ($strData1) $this->strDataInput = $strData1;
        if ($strData2) $this->strDataOutput = $strData2;
        $this->inizializzaData();

    }

    /**
     * @return string
     */
    public function getStrData()
    {
        return $this->strData;
    }

    /**
     * Forza il formato della data di ingresso
     *
     * @param string $strDataInput
     */
    public function settaFormatoLettura($strDataInput)
    {
        $this->strDataInput = $strDataInput;
    }

    /**
     * @return string
     */
    public function getStrDataInput()
    {
        return $this->strDataInput;
    }

    /**
     * Forza il formato della data di uscita
     *
     * @param string $strDataOutput
     */
    public function settaStringaSalvataggio($strDataOutput)
    {
        $this->strDataOutput = $strDataOutput;
    }

    /**
     * @return string
     */
    public function getStrDataOutput()
    {
        return $this->strDataOutput;
    }

    /**
     * Forza il periodo
     *
     * @param null $strMove
     */
    public function settaPeriodo($strMove)
    {
        $this->strMove = $strMove;
        $this->spostaData();
    }

    /**
     * @return null
     */
    public function getStrMove()
    {
        return $this->strMove;
    }

    private $strData;
    private $boolToSave;
    private $strDataInput;
    private $strDataOutput;
    private $boolForce;
    /** @var  DateTime */
    private $objDate;
    private $strMove;

    function __toString()
    {
        return $this->valore();
    }

    public function valore()
    {
        if (!$this->objDate) return '';
        $strReturn = ($this->boolToSave) ? $this->objDate->format($this->strDataOutput) : $this->objDate->format($this->strDataInput);

        return $strReturn;
    }

    private function spostaData()
    {
        if (!$this->strMove or !$this->objDate) return;
        $ultimoCarattere = substr($this->strMove, -1);
        $segno = ('-' == $ultimoCarattere) ? '-' : '+';
        $strSposta = str_replace($segno, '', $this->strMove);
        ('-' == $segno) ? $this->objDate->sub(new DateInterval($strSposta)) : $this->objDate->add(new DateInterval($strSposta));
        $this->strMove = null;
    }

    private function inizializzaData()
    {
        if ('now' != $this->strData) {
            $str = preg_replace('/\D/', '', $this->strData);
            if (0 == $str) {
                $this->objDate = null;

                return;
            }
        }
        if (!$this->strData and !is_numeric($this->strData)) {
            $this->objDate = null;

            return;
        }
        $this->objDate = new DateTime();
        $this->objDate->setTimezone(new DateTimeZone(CLICK_TIMEZONE));
        if ('now' === $this->strData) {
            return;
        }
        $this->objDate = ($this->boolToSave) ? $this->objDate->createFromFormat($this->strDataInput, $this->strData) : $this->objDate->createFromFormat($this->strDataOutput, $this->strData);
        if (!$this->objDate) {
            return;
        }
        $errori = $this->objDate->getLastErrors();
        if ($errori['errors'] or ($this->boolForce and $errori['warnings'])) $this->objDate = null;
    }

    function __construct($strData = 'now', $boolToSave = true, $strDataInput = 'd/m/Y', $strDataOutput = 'Y-m-d',
                         $strMove = null, $boolForce = false)
    {
        $this->strData = $strData;
        $this->boolToSave = $boolToSave;
        $this->boolForce = $boolForce;
        $this->strDataInput = $strDataInput;
        $this->strDataOutput = $strDataOutput;
        $this->strMove = $strMove;
        $this->inizializzaData();
        $this->spostaData();
    }
}

/**
 * Created by JetBrains PhpStorm.
 * User: mauro.zuccato
 * Date: 23/05/12
 * Time: 15.44
 * To change this template use File | Settings | File Templates.
 */
/* * ************************************************************ */

function connetti()
{

    $connessione = mysql_connect(CLICK_SERVER, CLICK_NAME, CLICK_PWD);
    if (!$connessione) {
        setIsRollback(1);

        return false;
    }
    $dbSelect = mysql_select_db(CLICK_DATABASE, $connessione);
    if (!$dbSelect) {
        setIsRollback(1);
        mysql_close($connessione);

        return false;
    }

    return $connessione;
}

/* * ************************************************************** */

function disconnetti($connessione)
{
    @mysql_close($connessione);
}

/***************************************************************/
function docRoot($strPath = ROOT_FOLDER, $boolCostante = true, $boolCreaCartella = false)
{
    if (!defined('DOC_ROOT') or !$boolCostante) {
        if ($strPath) {
            if (0 !== strpos($strPath, '/')) {
                $strPath = '/' . $strPath;
            }
        }
        $docRoot = $_SERVER['DOCUMENT_ROOT'];
        if (isset($_SERVER['PATH_TRANSLATED'])) $pathTranslate = $_SERVER['PATH_TRANSLATED'];
        $phpSelf = $_SERVER['PHP_SELF'];
        if (isset($_SERVER['SCRIPT_FILENAME'])) $scriptFilename = $_SERVER['SCRIPT_FILENAME'];
        if (!isset($docRoot))
            if (isset($scriptFilename))
                $docRoot = str_replace('', '/', substr($scriptFilename, 0, 0 - strlen($phpSelf)));
            elseif (isset($pathTranslate))
                $docRoot = str_replace('', '/', substr(str_replace('', '', $pathTranslate), 0, 0 - strlen($phpSelf)));
        $docRoot = str_replace('\\', '/', $docRoot);
        if (!$strPath or substr($strPath, -1) != '/') $strPath .= '/';
        if ($boolCreaCartella) {
            if (!is_dir($docRoot . $strPath)) {
                mkdir($docRoot . $strPath);
            }
        }
        if (!$boolCostante) {
            return $docRoot . $strPath;
        }
        define('DOC_ROOT', $docRoot . $strPath);
        unset($docRoot, $pathTranslate, $phpSelf, $scriptFilename);
    }

    return null;
}

/**
 * @param mixed $cellaControllo
 *
 * @return bool|mixed
 */
function valore($cellaControllo)
{
    if (!isset($cellaControllo)) {
        return false;
    }

    return $cellaControllo;
}

/**
 * @param      $nomeArray
 * @param null $nomeCampo
 *
 * @return bool
 */
function letturaSicuraCampo($nomeArray, $nomeCampo = null)
{
    if (!isset($nomeArray)) {
        return false;
    }
    if (is_null($nomeCampo)) {
        return $nomeArray;
    }
    if (!isset($nomeArray[$nomeCampo])) {
        return false;
    }

    return $nomeArray[$nomeCampo];
}

/********************************************************************/
/**
 * @param        $strName
 * @param string $strType
 *
 * @return null
 */
function receiveData($strName, $strType = 'POST')
{
    $strType = strtoupper($strType);
    switch ($strType) {
        case 'POST':
            return (isset($_POST[$strName])) ? $_POST[$strName] : null;
            break;
        case 'GET':
            return (isset($_GET[$strName])) ? $_GET[$strName] : null;
            break;
        case 'REQUEST':
            return (isset($_REQUEST[$strName])) ? $_REQUEST[$strName] : null;
            break;
        case 'SESSION':
            return (isset($_SESSION[$strName])) ? $_SESSION[$strName] : null;
            break;
        case 'COOKIE':
            return (isset($_COOKIE[$strName])) ? $_COOKIE[$strName] : null;
            break;

    }

    return null;
}

/**
 * Funzione addDimension.
 *
 * @param int      $intSize
 * @param bool|int $maxLength
 * @param bool     $boolApostrofo
 *
 * @return string
 */
function addDimension($intSize = 0, $maxLength = false, $boolApostrofo = false)
{
    if (!$intSize and !$maxLength) return '';
    if (!$intSize and $maxLength) return ($boolApostrofo) ? " maxlength='$intSize' " : "  maxlength=\"$intSize\" ";
    if ($intSize and !$maxLength) {
        if (false === $maxLength) return ($boolApostrofo) ? " size='$intSize' " : " size=\"$intSize\" ";

        return ($boolApostrofo) ? " size='$intSize' maxlength='$intSize' " : " size=\"$intSize\" maxlength=\"$intSize\" ";
    }

    return ($boolApostrofo) ? " size='$intSize' maxlength='$maxLength' " : " size=\"$intSize\" maxlength=\"$maxLength\" ";
}

/**
 * @param bool $bool
 * @param null $mostraMetodo
 * @param bool $boolApostrofo
 *
 * @return string
 */
function hideThis($bool, $mostraMetodo = null, $boolApostrofo = false)
{
    $strStyle = 'style=';
    $strStyle .= ($boolApostrofo) ? '\'' : '"';
    if ($bool) {
        $strStyle .= 'display:none' . right($strStyle, 1);

        return $strStyle;
    }
    $mostraMetodo = strtoupper($mostraMetodo);
    switch ($mostraMetodo) {
        case 'I':
            $strStyle .= 'display:inline' . right($strStyle, 1);
            break;
        case 'B':
            $strStyle .= 'display:block' . right($strStyle, 1);
            break;
        case 'IB':
            $strStyle .= 'display:inline-block' . right($strStyle, 1);
            break;
        default:
            $strStyle = '';
            break;
    }

    return $strStyle;
}

/***************************************************************/
/**
 * @param $bool
 * @param $intApostrofi
 *
 * @return string
 */
function addSelected($bool, $intApostrofi = 0)
{
    switch ($intApostrofi) {
        case 1:
            $strSelected = ' selected=\'selected\'';
            break;
        case 2:
            $strSelected = ' selected="selected"';
            break;
        default:
            $strSelected = ' selected';
    }

    return ($bool) ? $strSelected : '';
}

/**
 * @param $bool
 * @param $intApostrofi
 *
 * @return string
 */
function addChecked($bool, $intApostrofi = 0)
{
    switch ($intApostrofi) {
        case 1:
            $strCheck = ' checked=\'checked\'';
            break;
        case 2:
            $strCheck = ' checked="checked"';
            break;
        default:
            $strCheck = ' checked';
    }

    return ($bool) ? $strCheck : '';
}

/**
 * @param $bool
 * @param $intApostrofi
 *
 * @return string
 */
function addDisabled($bool, $intApostrofi = 0)
{
    switch ($intApostrofi) {
        case 1:
            $strDisable = ' disabled=\'disabled\'';
            break;
        case 2:
            $strDisable = ' disabled="disabled"';
            break;
        default:
            $strDisable = ' disabled';
    }

    return ($bool) ? $strDisable : '';
}

/**
 * @param $bool
 * @param $intApostrofi
 *
 * @return string
 */
function addReadOnly($bool, $intApostrofi = 0)
{
    switch ($intApostrofi) {
        case 1:
            $strReadOnly = ' readonly=\'readonly\'';
            break;
        case 2:
            $strReadOnly = ' readonly="readonly"';
            break;
        default:
            $strReadOnly = ' readonly';
    }

    return ($bool) ? $strReadOnly : '';
}

/****************************************************************/
/**
 * Funzione per includere un file ove passato. Il programma verifica la posizione attuale del file e ritorna verso la
 * superficie.
 *
 * @param string $strFile2Include il nome del file (comprensivo di estensione)
 * @param string $strPathFromRoot il percorso ove il file da incluere e' tenuto (partendo dalla root, senza prima '/')
 *
 * @return string
 */
function requireInPath($strFile2Include, $strPathFromRoot = 'searchbar/')
{
    $strPathAttuale = $_SERVER['PHP_SELF'];
    $strPathAttuale = str_replace(ROOT_FOLDER, null, $strPathAttuale);
    $strPathToRoot = '';
    $arrayPath = explode('/', $strPathAttuale); //esplodendo la path attuale ho i valori 0 null, e ultimo con il nome del file. Inutili
    $intCartelle = count($arrayPath) - 2; //li elimino quindi dalla lista per sapere quanti salti indietro devo fare per raggiungere la root
    if ($intCartelle > 0) {
        for ($i = 0; $i < $intCartelle; $i++) $strPathToRoot .= '../';
    }

    return $strPathToRoot . $strPathFromRoot . $strFile2Include;
}

/****************************************************************/
/**
 * La funzione ritorna &nbsp; nel caso la stringa passata sia nulla.
 * FONDAMENTALE per mantenere la struttura div
 *
 * @param string $strValore
 *
 * @return string
 */
function emptyDiv($strValore)
{

    return ((!trim($strValore) and !is_numeric($strValore)) or '0000-00-00' == $strValore or '0000-00-00 00:00:00' == $strValore) ? '&nbsp;' : $strValore;
}

/***************************************************************/
/**
 * @param $value
 * @param $count
 *
 * @return string
 */
function right($value, $count)
{
    return substr($value, ($count * -1));
}

/***************************************************************/
/**
 * @param $string
 * @param $count
 *
 * @return string
 */
function left($string, $count)
{
    return substr($string, 0, $count);
}

function recuperaOra($stringaOra)
{
    $stringaNumerica = trim($stringaOra);
    if (is_numeric($stringaNumerica) and ($stringaNumerica == round($stringaNumerica, 0))) {
        $ora = $stringaNumerica % 24;

        return sprintf('%02d%02d', $ora, 0);
    }
    //eseguo il trimming di tutti i quei dati che non siano numeri in cui l'ora possa essere avvolta
    $primaCifra = posizioneNumero($stringaOra);
    $ultimaCifra = posizioneNumero($stringaOra, true);
    $strOra = '';
    if ($primaCifra !== false and $ultimaCifra) {
        $strOra = substr($stringaOra, $primaCifra, ($ultimaCifra - $primaCifra + 1));
        $strOra .= '';
        if ($strOra) {
            $separatore = preg_replace("/[0-9]/", "", $strOra);
            $oraMinuti = explode($separatore, $strOra);
            if (1 == count($oraMinuti)) {
                $oraMinuti[1] = 0;
            }
            $strOra = sprintf('%02d%02d', $oraMinuti[0], $oraMinuti[1]);
        }
    }

    return $strOra;

}

function posizioneNumero($stringa, $boolFondo = false)
{
    if ($boolFondo) {
        $pos = strlen($stringa) - 1;
        while ($pos >= 0) {
            if (ctype_digit($stringa[$pos])) {
                return $pos;
            }
            --$pos;
        }

    }
    else {
        $pos = 0;
        while ($pos < strlen($stringa)) {
            if (ctype_digit($stringa[$pos])) {
                return $pos;
            }
            ++$pos;
        }

    }

    return false;
}

/***************************************************************/
function get_server()
{
    $protocol = 'http';
    if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') {
        $protocol = 'https';
    }
    $host = $_SERVER['HTTP_HOST'];
    $baseUrl = $protocol . '://' . $host;
    if (substr($baseUrl, -1) == '/') {
        $baseUrl = substr($baseUrl, 0, strlen($baseUrl) - 1);
    }

    return $baseUrl;
}

/****************************************************/
/**
 * La funzione permette di visualizzare una select "guardare ma non toccare"
 * la select / option cui questo parametro sia impostato, ed a true, se modificata
 * torna al valore precedente
 *
 * @param bool $boolCondizione   condizione di blocco
 * @param null $strEventOnChange funzione onChange alternativa. Verra' usata se $boolCondizione == false
 *
 * @return null|string
 */
function unvariableSelect($boolCondizione, $strEventOnChange = null)
{
    if ($boolCondizione) {
        return ' onfocus="old=this.selectedIndex" onchange="this.selectedIndex=old" ';
    }
    if ($strEventOnChange) {
        return 'onchange ="' . $strEventOnChange . '" ';
    }

    return '';
}

/*****************************************************/
/**
 * La funzione prepara l'ID per essere verificato
 * se è numerico, torna '=numID'
 * se è una stringa senza virgole torna '="numID"'
 * se è una stringa con virgole, separa i campi in array, procedendo a controllarlo come tale
 * se è un array, per ogni dato, chiama prepareDataToDb per
 * elaborarlo nel modo utile, per poi fondere in un ' IN (numID1,numID2,...numIDn)'
 *
 * @param $idHandler
 *
 * @return string
 */
function prepareIdToDb($idHandler)
{
    if (is_numeric($idHandler)) return '=' . $idHandler;
    if (strstr($idHandler, ',') === false and !is_array($idHandler)) return '="' . $idHandler . '"';
    if (strstr($idHandler, ',') !== false) $idHandler = explode(',', $idHandler);
    foreach ($idHandler as $key => $value) {
        $idHandler[$key] = prepareDataToDb($value);
    }
    $idHandler = implode(',', $idHandler);

    return ' IN (' . $idHandler . ')';
}

/**
 * La funzione, al dato passato, se non numerico, aggiunge le doppie virgolette
 * le aggiunge inoltre per quegli interi che iniziano con 0
 *
 * @param $dato
 *
 * @return string
 */
function prepareDataToDb($dato)
{
    if ($dato === false) $dato = 0;
    if ($dato === true) $dato = 1;
    $arrayTemp = str_split($dato, 1);

    return (is_float($dato) or (is_numeric($dato) and ($arrayTemp[0] != 0))) ? $dato : '"' . $dato . '"';
}

/**
 * Funzione per eseguire la query di un database
 *
 * @param string $strQuery        stringa della query
 * @param string $chrValue        contratti di risposta
 * @param bool   $boolNumRow      se true, viene sommato all'array in ritorno, un intero corrispondente alla riga
 * @param null   $optMode         se passato, cambia il contratti di ritorno di mysql_fetch_array
 * @param bool   $boolKeepConnect normalmente false, se true non esegue la connessione / disconnessione
 *
 * @return int
 */
function queryDb($strQuery, $chrValue = 'b', $boolNumRow = false, $optMode = null, $boolKeepConnect = false)
{
    if (!$strQuery) return false;
    $chrValue = strtolower($chrValue);


    if (!$boolKeepConnect) {
        if (!$connessione = connetti()) {
            return false;
        }

    }
    if ($boolNumRow) {
        mysql_query('SET @rownum=-1');
        $strQuery = str_ireplace('SELECT ', 'SELECT (@rownum:=@rownum+1) as LineaRecord,', $strQuery);

    }
    $query = mysql_query($strQuery);
    if (!$boolKeepConnect) {
        disconnetti($connessione);

    }
    if (false === $query and 'b' != $chrValue) return false;
    switch ($chrValue) {
        case 'b':
            return $query;
        case 'c':
            return mysql_num_rows($query);
        case 'f':
            return ($optMode) ? mysql_fetch_array($query, $optMode) : mysql_fetch_array($query);
        case 'v':
            $result = mysql_fetch_array($query);

            return $result[0];
            break;
        default:
            return false;
    }
}

/***********************************************************************/
/**
 * piccola funzione di 'debug'. Torna le possibili imprecisione dei campi passati
 *
 * @param int $intErrore
 */
function debugDb($intErrore = 0)
{
    switch ($intErrore) {
        case 1:
            $strErrore = 'La tabella deve avere un valore!';
            break;
        case 2:
            $strErrore = 'Il valore od il campo di ricerca su cui effettuare il WHERE non sono passati';
            break;
        case 3:
            $strErrore = 'Solo uno dei due parametri risulta un\'array. Verificare il passaggio dei dati';
            break;
        case 4:
            $strErrore = 'I due array non sono lunghi uguali. Verificare i campi ed i valori passati';
            break;
        case 5:
            $strErrore = 'Anche nel caso di singolo valore, il campo non puo\' avere valore nullo!';
            break;
        default:
            return;
    }
    ?>
    <script type="text/javascript">
        <!--//
        alert('<?php echo $strErrore;?>');
        //-->
    </script>
    <?php
}

/**********************************************************************/
/**
 * Funzione per aggiornare il contenuto di una tabella nel database.
 * Parametri
 *
 * @static
 *
 * @param        $strTabella  - nome della tabella
 * @param        $arrayCampi  - array dei campi da aggiornare
 * @param        $arrayValori - array dei valori da aggiornare
 * @param        $intID       - identificatore della riga. Può essere un intero, una stringa, un array od una stringa
 *                            separata da virgole
 * @param string $strID       - nome campo da usare come identificatore (se non passato = ID)
 * @param bool   $boolAffectRow
 *
 * @return bool
 */
function updateDb($strTabella, $arrayCampi, $arrayValori, $intID, $strID = 'ID', $boolAffectRow = false)
{
    /*Torniamo indietro se i valori non sono inseriti, o gli array non sono grossi uguali*/
    setIsRollback(0);
    if (!$strTabella) {
        debugDb(1);
        setIsRollback(1);

        return false;
    }
    if (!$intID or !$strID) {
        debugDb(2);
        setIsRollback(1);

        return false;
    }
    if (is_array($arrayCampi) != (is_array($arrayValori))) {
        debugDb(3);
        setIsRollback(1);

        return false;
    }
    if (is_array($arrayCampi) and (count($arrayCampi) != count($arrayValori))) {
        debugDb(4);
        setIsRollback(1);

        return false;
    }
    if (!$arrayCampi) {
        debugDb(5);
        setIsRollback(1);

        return false;

    }


    if (!is_array($arrayCampi)) {
        $query = 'UPDATE ' . $strTabella . ' SET ' . $arrayCampi . '=';
        $query .= prepareDataToDb($arrayValori) . ' WHERE ' . $strID . prepareIdToDb($intID);
    }
    else {
        foreach ($arrayValori as $chiave => $valore) {
            $arrayValori[$chiave] = $arrayCampi[$chiave] . '=' . prepareDataToDb($valore);
        }
        $intConta = count($arrayValori);
        $query = 'UPDATE ' . $strTabella . ' SET ' . implode(',', $arrayValori);
        $query .= ' WHERE ' . $strID . prepareIdToDb($intID);
    }
    if (!$connessione = connetti()) {
        setIsRollback(1);

        return false;
    }

    beginTransition();

    $lastResult = mysql_query($query);
    if ($lastResult) {
        commitTransition();
    }
    else {
        rollbackTransition();
        setIsRollback(1);
    }

    disconnetti($connessione);
    if ($lastResult and $boolAffectRow) return mysql_affected_rows();

    return $lastResult;

}

/**********************************************************************/
/**
 * Funzione per inserire direttamente un valore in una tabella
 * Richiede
 *
 * @static
 *
 * @param        $strTabella  - nome della tabella
 * @param        $arrayCampi  - array con il nome dei campi da riempire
 * @param        $arrayValori - array con i valori da salvare
 * @param bool   $boolLastID  - boolean. Se passato true, la funzione ritorna l'ultimo ID inserito
 * @param string $strId       - nome del campo chiave principale (in genere ID)
 *
 * @return bool
 */
function insertInDb($strTabella, $arrayCampi, $arrayValori, $boolLastID = false, $strId = 'ID')
{
    /*Torniamo indietro se i valori non sono inseriti, o gli array non sono grossi uguali*/
    setIsRollback(0);
    if (!$strTabella) {
        debugDb(1);
        setIsRollback(1);

        return false;
    }
    if (is_array($arrayCampi) != (is_array($arrayValori))) {
        debugDb(3);
        setIsRollback(1);

        return false;
    }
    if (is_array($arrayCampi) and (count($arrayCampi) != count($arrayValori))) {
        debugDb(4);
        setIsRollback(1);

        return false;
    }
    if (!$arrayCampi) {
        debugDb(5);
        setIsRollback(1);

        return false;

    }
    /*****************************************************************/
    if (!is_array($arrayCampi)) {
        $query = 'INSERT INTO ' . $strTabella . ' (' . $arrayCampi . ') VALUES (' . prepareDataToDb($arrayValori) . ')';
    }
    else {
        foreach ($arrayValori as $chiave => $valore) {
            $arrayValori[$chiave] = prepareDataToDb($valore);
        }
        $query = 'INSERT INTO ' . $strTabella . ' (' . implode(',', $arrayCampi) . ') VALUES (' . implode(',', $arrayValori) . ')';
    }


    if (!$connessione = connetti()) {
        setIsRollback(1);

        return false;
    }

    beginTransition();
    $lastResult = mysql_query($query);
    if ($lastResult) {
        commitTransition();
        $intLastID = mysql_fetch_array(mysql_query('SELECT MAX(' . $strId . ') FROM ' . $strTabella));
        $intLastID = $intLastID[0];
    }
    else {
        rollbackTransition();
        setIsRollback(1);
        $intLastID = false;
    }

    disconnetti($connessione);
    if ($lastResult and $boolLastID) return $intLastID;

    return $lastResult;

}

/**
 * Richiamare la funzione per abilitare la transizione
 */
function beginTransition()
{
    mysql_query('SET autocommit = 0');
    mysql_query('BEGIN');

}

function phpToAlert($strMessage, $boolFromHtmlEntities = true)
{
    if ($boolFromHtmlEntities) $strMessage = html_entity_decode($strMessage);
    $strMessage = str_replace('\'', "\'", $strMessage);
    $messaggi = explode(PHP_EOL, $strMessage);
    $strMessage = implode('\n', $messaggi);
    ?>
    <script type="text/javascript">
        <!--//
        alert('<?php echo $strMessage;?>');
        //-->
    </script>
    <?php
}

function phpToWaiting($strMessage, $boolFromHtmlEntities = true)
{
    if ($boolFromHtmlEntities) $strMessage = html_entity_decode($strMessage);
    $strMessage = str_replace('\'', "\'", $strMessage);
    ?>
    <script type="text/javascript">
        <!--//
        $('#waitingMsg').text('<?php echo $strMessage?>');
        //-->
    </script>
    <?php
}

/**
 * Richiamare la funzione per eseguire il rollback della transizione
 */
function rollbackTransition()
{
    mysql_query('ROLLBACK');
    mysql_query('SET autocommit = 1');
    //phpToAlert('ATTENZIONE: Si &egrave; verificato un errore. Eseguito rollback!');
}

/**
 * Richiamarare la funzione per eseguire il commit (renderla attiva) della transizione
 */
function commitTransition()
{
    mysql_query('COMMIT');
    mysql_query('SET autocommit = 1');
    //phpToAlert('Elaborazione avvenuta correttamente');
}

function modificaApici($strModifica, $boolEncode)
{
    if ($boolEncode) {
        $strFind = '\'';
        $strReplace = '\\\'';
    }
    else {
        $strReplace = '\'';
        $strFind = '\\\'';
    }

    return str_replace($strFind, $strReplace, $strModifica);
}

/*****************************************************************************/
/**
 * Funzione che ritorna il prossimo valore utile di ID. Se non trovato
 * ritorna il primo valido
 *
 * @param string              $strTabella       la tabella in cui cercare
 * @param int                 $intID            l'ID da cui partire la ricerca
 * @param string              $strOrder         il campo per cui ordinare gli elementi. ATTENZIONE DEVE ESSERE
 *                                              NUMERICO, NOT NULL e UNICO!
 * @param bool                $boolIncludiNuovo se true permette di circolare tra i record... includendo il nuovo.
 * @param string|null|boolean $stringWhere      stringa di filtro sui risultati. Se false filtra su ID_CONDOMINIO, se
 *                                              null su nessun campo
 *
 * @return int l'ID ritrovato
 */
function moveForward($strTabella, $intID, $strOrder = 'ID', $boolIncludiNuovo = false, $stringWhere = false)
{
    if (false === $stringWhere) {
        $stringWhere = 'ID_CONDOMINIO=' . readIdSessione('condominio');
    }
    if (!is_null($stringWhere)) {
        $whereCase = ' WHERE ' . $stringWhere;
        $andCase = ' AND ' . $stringWhere;
    }
    if ('ID' != $strOrder) $intID = queryDb("SELECT {$strOrder} FROM {$strTabella} WHERE ID={$intID}", 'v');
    if (!$intID) $intID = 0;
    $intNewID = queryDb("SELECT ID FROM {$strTabella} WHERE ({$strOrder} > {$intID} " . "{$andCase}) ORDER BY {$strOrder} ASC LIMIT 1", 'v');
    if (!$intNewID) $intNewID = ($boolIncludiNuovo) ? 0 : queryDb("SELECT ID FROM {$strTabella} {$whereCase} ORDER BY {$strOrder} ASC LIMIT 1", 'v');

    return $intNewID;
}

/*************************************************************************/
/**
 * Funzione che ritorna il precedente valore utile di ID. Se all'inizio,
 * ritorna il lavoro piu' elevato.
 *
 * @param string              $strTabella       la tabella in cui cercare
 * @param int                 $intID            l'ID da cui partire la ricerca
 * @param string              $strOrder         il campo per cui ordinare gli elementi. ATTENZIONE DEVE ESSERE
 *                                              NUMERICO, NOT NULL e UNICO!
 * @param bool                $boolIncludiNuovo se true permette di circolare tra i record... includendo il nuovo.
 * @param string|null|boolean $stringWhere      stringa di filtro sui risultati. Se false filtra su ID_CONDOMINIO, se
 *                                              null su nessun campo
 *
 * @return int l'ID ritrovato
 */
function moveBackward($strTabella, $intID, $strOrder = 'ID', $boolIncludiNuovo = false, $stringWhere = false)
{
    if (false === $stringWhere) {
        $stringWhere = 'ID_CONDOMINIO=' . readIdSessione('condominio');
    }
    if (!is_null($stringWhere)) {
        $whereCase = ' WHERE ' . $stringWhere;
        $andCase = ' AND ' . $stringWhere;
    }

    if ('ID' != $strOrder) $intID = queryDb("SELECT {$strOrder} FROM {$strTabella} WHERE ID={$intID}", 'v');

    if (!$intID) {
        $intID = queryDb("SELECT {$strOrder} FROM {$strTabella}  {$whereCase} ORDER BY {$strOrder} DESC LIMIT 1", 'v');
        ++$intID;
    }

    $intNewID = queryDb("SELECT ID FROM {$strTabella} WHERE ({$strOrder} < {$intID} " . " {$andCase}) ORDER BY {$strOrder} DESC LIMIT 1", 'v');

    if (!$intNewID) $intNewID = ($boolIncludiNuovo) ? 0 : queryDb("SELECT ID FROM {$strTabella} {$whereCase} ORDER BY {$strOrder} DESC LIMIT 1", 'v');

    return $intNewID;
}

function InsertFormInDb($strTabella, $arrayCampi, $arrayValori, $intID)
{
    if ($intID) {
        updateDb($strTabella, $arrayCampi, $arrayValori, $intID);
    }
    else {
        insertInDb($strTabella, $arrayCampi, $arrayValori);
    }
}

/**
 * La funzione converte una stringa di dati in Array. Inoltre permette di
 * tradurre i segmenti nelle chiavi dell'array invece che nei suoi valori
 *
 * @param string $stringa
 * @param bool   $boolChiavi
 * @param string $separatore
 *
 * @return array
 */
function stringToArray($stringa, $boolChiavi = false, $separatore = ',')
{
    if (!trim($stringa) and 0 !== $stringa) return array();
    $array = explode($separatore, $stringa);
    if (!$boolChiavi) return $array;
    foreach ($array as $chiave => $valore) $array[$chiave] = trim($valore);
    $array = array_flip($array);
    foreach ($array as $chiave => $valore) $array[$chiave] = null;

    return $array;
}

function evidenziaRosso($string = '*', $id = null)
{
    if ($id) {
        $id = ' id="' . $id . '"';
    }
    ?><span class="testoevrosso" <?php echo $id ?>><?php echo $string ?></span><?php
}

function evidenzia($string, $boolRichiesto = false, $id = null)
{
    if ($id) {
        $id = ' id="' . $id . '"';
    }
    ?><span class="testoev" <?php echo $id ?>><?php echo $string ?></span><?php
    if ($boolRichiesto) evidenziaRosso();
}

/**
 * Exactly the same as the mysql_query function, except that it will throw an
 * Exception instead of returning false when the query failed
 *
 * @param string   The query you want to run on the MySQL server
 * @param resource The connection you would like to use to run the query at
 *
 * @return resource The result of running the query on the server
 * @throws Exception Whenever the query failed
 */
function e_mysql_query($query, $link_identifier = NULL)
{
    $result = @mysql_query($query, $link_identifier);
    if (!$result)
        throw new Exception (sprintf("MySQL.Error(%d): %s", mysql_errno(), mysql_error()));

    return $result;
}

function attivaResizeSelect()
{
    echo ' onmouseover="resizeSelectOption(this.id);" onclick="holdSelectOption(this.id);" onmouseout="resizeSelectOption(this.id);" ';
}

function onChangeResizeSelect()
{
    echo 'resizeSelectOption(this.id);';
}

/**
 * Funzione per duplicare una riga di un database
 *
 * @param string            $strTabella       nome della tabella
 * @param string|array|null $arrayCampi       array dei campi da copiare. Se non specificato recupero tutti i campi
 *                                            della tabella tranne la chiave primaria ($strNomeCampo)
 * @param int               $intValore        numero di riga da copiare
 * @param bool              $boolTornaNuovoID se true, torna l'ultimo ID (campo) inserito
 * @param string            $strNomeCampo     nome campo chiave primaria
 *
 * @return null|int ID inserito
 */
function duplicaRecord($strTabella, $arrayCampi, $intValore, $boolTornaNuovoID = false, $strNomeCampo = 'ID')
{
    if (!is_numeric($intValore)) return null;
    if (is_array($arrayCampi)) $arrayCampi = implode(',', $arrayCampi);
    else if (!$arrayCampi) $arrayCampi = queryDb('SELECT GROUP_CONCAT(DISTINCT COLUMN_NAME) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME="' . $strTabella . '" AND COLUMN_NAME <> "' . $strNomeCampo . '" ', 'v');
    $rigaDaCopiare = queryDb("SELECT $arrayCampi FROM $strTabella WHERE $strNomeCampo=$intValore", 'f', false, MYSQL_NUM);
    $arrayCampi = explode(',', $arrayCampi);
    $lastResult = insertInDb($strTabella, $arrayCampi, $rigaDaCopiare, $boolTornaNuovoID, $strNomeCampo);

    return $lastResult;
}

/**
 * La funzione torna i campi della tabella esclusi quelli passati sottoforma di array
 * o di campo unico
 *
 * @param      $strTabella
 * @param null $strIgnore
 *
 * @return string
 */
function ritornaCampi($strTabella, $strIgnore = null)
{
    if (is_array($strIgnore)) {
        foreach ($strIgnore as $id => $valore) $strIgnore[$id] = '\'' . $strIgnore . '\'';
        $strIgnore = implode(',', $strIgnore);
    }
    else if ($strIgnore) $strIgnore = '\'' . $strIgnore . '\'';
    $strWhere = ($strIgnore) ? " AND COLUMN_NAME NOT IN ($strIgnore)" : null;

    return queryDb('SELECT GROUP_CONCAT(DISTINCT COLUMN_NAME) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME="' . $strTabella . '"' . $strWhere, 'v');
}

function ritornaNumero($strStringa, $boolPercentuale = false, $boolIntero = false)
{
    $numero = trim(str_replace(',', '.', $strStringa)); // trimming e sostituzione della virgola in punto
    $numero = ($boolIntero) ? (int)$numero : (float)$numero;
    if ($boolPercentuale) $numero /= 100;

    return $numero;
}

/**
 * Funzione di ritorno della data in formato utile, partendo dalla string 01/01/1900
 *
 * @param        $strStringa   data in stringa
 * @param bool   $boolInData   se true ritorna la data nel formato utile per il salvataggio su MySQL
 * @param string $delimitatore il delimitatore delle informazioni, di default '/'
 *
 * @return int|null|string
 */
function ritornaData($strStringa, $boolInData = false, $delimitatore = '/')
{
    $arrayData = explode($delimitatore, $strStringa);
    if (!isset($arrayData[2])) return null;
    $giorno = intval($arrayData[0]);
    $mese = intval($arrayData[1]);
    $anno = intval($arrayData[2]);
    if ($anno < 40) $anno += 2000;
    else if ($anno < 100) $anno += 1900;
    if (!$giorno or !$mese) return null;
    $data = mktime(0, 0, 0, $mese, $giorno, $anno);

    return ($boolInData and $data) ? date("Y/m/d", $data) : $data;
}

function codificaHtml($strToEncode)
{
    return preg_replace('/[\x80-\xFF]/e', '"&#x".dechex(ord("$0")).";"', $strToEncode);
}

/**
 * Funzione per formattare i numeri.
 *
 * @param         $numero      numero da formattare
 * @param int     $intDecimali numero di decimali (default=4)
 * @param boolean $boolPuntini se true separa le migliaia con il punto (default=false)
 * @param null    $unitaMisura se passato, accoda il descrittore unitario
 *
 * @return string
 */
function formattaNumero($numero, $intDecimali = 4, $boolPuntini = false, $unitaMisura = null)
{
    if (!is_numeric($numero)) return $numero;
    $migliaia = ($boolPuntini) ? '.' : '';
    $string = number_format($numero, $intDecimali, ',', $migliaia);
    if ($unitaMisura) $string .= ' ' . $unitaMisura;

    return $string;
}

/**
 * Mostra (o nasconde) la maschera di attesa esecuzione
 *
 * @param bool $boolMostra
 */
function mostraAttesa($boolMostra = true)
{
    $strEsegui = ($boolMostra) ? 'true' : 'false';
    ?>
    <script type="text/javascript">
        mostraAttesa(<?php echo $strEsegui?>);
    </script>
    <?php
}

/**
 * Funzione di inizio calcolo tempo esecuzione
 *
 * @return mixed
 */
function startTimer()
{
    $mtime = microtime();
    $mtime = explode(" ", $mtime);
    $mtime = $mtime[1] + $mtime[0];

    return $mtime;
}

/**
 * Blocco del tempo di esecuzione e ritorno. Richiesto tempo di partenza
 *
 * @param float    $starttime
 * @param int|bool $numSecondiMax
 *
 * @return string
 */
function stopTimer($starttime, $numSecondiMax = 150)
{
    $mtime = microtime();
    $mtime = explode(" ", $mtime);
    $mtime = $mtime[1] + $mtime[0];
    $endtime = $mtime;
    $totaltime = round($endtime - $starttime, 4);

    if (is_numeric($numSecondiMax) and ($totaltime > $numSecondiMax)) {
        $valore = round($totaltime / 60, 4);

        return 'Eseguito in ' . $valore . ' minuti.';
    }

    return 'Eseguito in ' . $totaltime . ' secondi';
}

/**
 * Funzione che altera una stringa in modo che possibili doppi apici " diventino il loro codice speciale HTML
 *
 * @param string $stringa
 *
 * @param bool   $boolEmpty
 *
 * @return mixed|string
 */
function outString($stringa, $boolEmpty = true)
{
    if (!$stringa and !is_numeric($stringa)) return ($boolEmpty) ? emptyDiv($stringa) : '';
    $arraySearch = array('"', '<', '>', "\'");
    $arrayReplace = array('&#34;', '&#60;', '&#62;');

    return str_replace($arraySearch, $arrayReplace, $stringa);
}

/**
 * @param null $strId
 * @param      $boolSovrascrivi boolean
 *
 * @return resource
 */
function openLogErr($strId = null, $boolSovrascrivi = false)
{
    docRoot(ROOT_FOLDER);
    $accesso = ($boolSovrascrivi) ? 'w+' : 'a+';
    $logsRoot = DOC_ROOT . 'logs/';
    if (!is_dir($logsRoot)) {
        mkdir($logsRoot);
        chmod($logsRoot, 0777);
    }
    $mese = date('y-m/');
    $logsRoot .= $mese;
    if (!is_dir($logsRoot)) {
        mkdir($logsRoot);
        chmod($logsRoot, 0777);
    }
    $nomeFile = "log_{$strId}.log";
    $boolCreate = !is_file($logsRoot . $nomeFile);
    $handler = fopen($logsRoot . $nomeFile, $accesso);
    if (!$handler) return false;
    if ($boolCreate) chmod($logsRoot . $nomeFile, 0777);

    return $handler;
}

/**
 * @param null $strId
 *
 * @return bool|string
 */
function readLogLine($strId = null)
{
    docRoot(ROOT_FOLDER);
    $logFile = DOC_ROOT . 'logs/' . date('y-m/') . "log_{$strId}.log";
    if (!file_exists($logFile)) return false;
    $handler = fopen($logFile, 'r');
    $dato = fgets($handler);
    fclose($handler);

    return $dato;
}

/**
 * @param resource $handler
 */
function closeLogErr(&$handler)
{
    fclose($handler);
    $handler = null;
}

/**
 *
 * @param string        $strMessage
 * @param string        $strId
 * @param resource|null $log
 *
 * @return resource
 */
function logErrore($strMessage, $strId = null, $log = null)
{
    if (!$strMessage) return;
    $strMessage = date('d H:i:s ') . $strMessage . PHP_EOL;
    if (!$log) {
        docRoot(ROOT_FOLDER);
        $logsRoot = DOC_ROOT . 'logs/';
        if (!is_dir($logsRoot)) {
            mkdir($logsRoot);
            chmod($logsRoot, 0777);
        }
        $mese = date('y-m/');
        $logsRoot .= $mese;
        if (!is_dir($logsRoot)) {
            mkdir($logsRoot);
            chmod($logsRoot, 0777);
        }
        $nomeFile = "log_{$strId}.log";
        $boolCreate = !is_file($logsRoot . $nomeFile);
        $handler = fopen($logsRoot . $nomeFile, 'a+');
        if (!$handler) return;
        if ($boolCreate) chmod($logsRoot . $nomeFile, 0777);

    }
    else $handler = $log;
    fwrite($handler, $strMessage);
    if (!$log) fclose($handler);
}

/**
 *
 * @param string   $strMessage
 * @param resource $log
 */
function logErr($strMessage, $log)
{
    if (!$strMessage) return;
    $strMessage = date('d H:i:s ') . $strMessage . PHP_EOL;
    if (!$log) return;
    fwrite($log, $strMessage);
}

/**
 * La funzione mostra un'immagine fissa al posto del checkbox
 *
 * @param bool $bool
 *
 * @param null $strID
 * @param bool $boolHide
 *
 * @return string
 */
function readOnlyCheck($bool = false, $strID = null, $boolHide = false)
{
    if (is_null($bool)) {
        $str = ROOT_FOLDER . '/grafica/neutro.png';
    }
    else {
        $str = ($bool) ? ROOT_FOLDER . '/grafica/flagged.png' : ROOT_FOLDER . '/grafica/unflagged.png';
    }
    $strPerDisplay = '';
    if ($strID) {
        $strPerDisplay = ($boolHide) ? 'none' : 'inline';
        $strPerDisplay = ' id="' . $strID . '" style="display:' . $strPerDisplay . '"';
    }

    return '<img src="' . $str . '" ' . $strPerDisplay . ' />';
}

/**
 * @param string $dataLetta
 * @param string $formatoVuota
 *
 * @return string
 */
function outDate($dataLetta, $formatoVuota = '00/00/00')
{
    return ($dataLetta == $formatoVuota) ? '' : $dataLetta;
}

/**
 * Funzione di visualizzazione di un array in forma di dichiarazione
 *
 * @param string $nomeArray       nome dell'array da mostrare
 * @param array  $array           nome dell'array da leggere
 * @param bool   $boolAssociativo se true, mostra i valori delle chiavi, altrimenti solo i valori
 */
function dumpArray($nomeArray, $array, $boolAssociativo = true)
{
    if ('$' != $nomeArray[0]) $nomeArray = '$' . $nomeArray;
    $numValori = count($array);
    $ciclo = 0;
    echo "{$nomeArray}=array(<br/>";
    $contatore = 1;
    foreach ($array as $id => $valore) {
        if (!is_numeric($valore) or (0 != $valore and "0" == $valore[0])) $valore = '\'' . $valore . '\'';
        if (!is_numeric($id)) $id = '\'' . $id . '\'';
        echo ($boolAssociativo or !is_numeric($id)) ? "$id=>$valore" : $valore;
        if ((++$ciclo) < $numValori) echo ",";
        if (!($contatore % 8)) echo "<br/>";
        ++$contatore;
    }
    echo ");<br/>";
}

/**
 * Funzione che ritorna true o false in base a se l'array e' totalmente associativo o no
 *
 * @param $array array da controllare
 *
 * @return bool
 */
function boolArrayAssociativo($array)
{
    return !(count($array) - count(array_filter(array_keys($array), 'is_string')));
}

/**
 * @param string $strData
 * @param bool   $boolToSave
 * @param string $strDataInput
 * @param string $strDataOutput
 *
 * @return string|DateTime
 */
function trasformaData($strData = 'now', $boolToSave = true, $strDataInput = 'd/m/Y', $strDataOutput = 'Y-m-d')
{
    if ('now' != $strData) {
        $str = preg_replace('/\D/', '', $strData);
        if (0 == $str) return '';
    }
    if (!$strData and !is_numeric($strData)) return '';
    $objDate = new DateTime();
    if ('now' != $strData) $objDate = ($boolToSave) ? $objDate->createFromFormat($strDataInput, $strData) : $objDate->createFromFormat($strDataOutput, $strData);
    if (!$objDate) return '';
    $errore = $objDate->getLastErrors();
    if ($errore['errors']) return '';
    if (strtolower($boolToSave) == 'obj') return $objDate;
    $ritorno = ($boolToSave) ? $objDate->format($strDataOutput) : $objDate->format($strDataInput);
    unset($objDate);

    return $ritorno;
}

/**
 * @param string $strData
 * @param bool   $boolToSave
 * @param string $strDataInput
 * @param string $strDataOutput
 *
 * @return string|DateTime
 */
function trasformaDataForce($strData = 'now', $boolToSave = true, $strDataInput = 'd/m/Y', $strDataOutput = 'Y-m-d')
{
    if ('now' != $strData) {
        $str = preg_replace('/\D/', '', $strData);
        if (0 == $str) return '';
    }
    if (!$strData and !is_numeric($strData)) return '';
    $objDate = new DateTime();
    if ('now' != $strData) $objDate = ($boolToSave) ? $objDate->createFromFormat($strDataInput, $strData) : $objDate->createFromFormat($strDataOutput, $strData);
    if (!$objDate) return '';
    $errore = $objDate->getLastErrors();
    if ($errore['errors'] or $errore['warnings']) return '';
    if (strtolower($boolToSave) == 'obj') return $objDate;
    $ritorno = ($boolToSave) ? $objDate->format($strDataOutput) : $objDate->format($strDataInput);
    unset($objDate);

    return $ritorno;
}

/**
 * Class DateItaliane
 */
class DateItaliane
{
    /** @var array Nome mesi estesi */
    static public $mesi = array(1 => 'Gennaio', 'Febbraio', 'Marzo', 'Aprile',
                                'Maggio', 'Giugno', 'Luglio', 'Agosto',
                                'Settembre', 'Ottobre', 'Novembre', 'Dicembre');
    /** @var array Nome mesi abbreviati */
    static public $shortmesi = array(1 => 'Gen', 'Feb', 'Mar', 'Apr',
                                     'Mag', 'Giu', 'Lug', 'Ago',
                                     'Set', 'Ott', 'Nov', 'Dic');

    static public $giorni = array('Domenica', 'Luned&igrave;', 'Marted&igrave;',
                                  'Mercoled&igrave;', 'Gioved&igrave;', 'Venerd&igrave;', 'Sabato');

    static private $dataPerLettere;

    /**
     * @return mixed
     */
    public static function getDataPerLettere()
    {
        return self::$dataPerLettere;
    }

    static public function dataLettere($dataMysql, $formattoInput = 'Y-m-d')
    {
        $tempData = new TrasformaData($dataMysql, true, $formattoInput, 'n');
        $mese = strtolower(DateItaliane::$mesi[$tempData->valore()]);
        $tempData->settaStringaSalvataggio('j-Y');
        $giornoAnno = $tempData->valore();
        DateItaliane::$dataPerLettere = str_replace('-', ' ' . $mese . ' ', $giornoAnno);
        $tempData = null;

        return DateItaliane::$dataPerLettere;
    }
}

/**
 * Funzione IMPORTOLETTERE
 *
 * @param number $importo
 *
 * @return string
 */
function valoreInLettere($importo)
{
    $segno = null; //indica se mostrare il segno negativo
    if ($importo < 0) {
        $segno = '-';
        $importo *= -1;
    }
    //fix per le cifre decimali. Prese come stringa per evitare che, se passo 10.1 o 10.01 la cifra, in numero, risulti 1.
    if (strpos($importo, '.')) {
        $cifre = explode('.', $importo);
        $decimali = $cifre[1];
        $cifra = $cifre[0];
    }
    else {
        $cifra = intval($importo);
        $decimali = 0;
    }
    $arrayCifreSingole = array(2 => 'due', 'tre', 'quattro', 'cinque', 'sei', 'sette', 'otto', 'nove', 'dieci', 'undici', 'dodici', 'tredici', 'quattordici', 'quindici', 'sedici', 'diciassette', 'diciotto', 'diciannove');
    $arrayCifreDecimi = array(2 => 'venti', 'trenta', 'quaranta', 'cinquanta', 'sessanta', 'settanta', 'ottanta', 'novanta');
    $centinaiaDiMigliaia = '';
    $migliaia = '';
    $centinaia = '';
    $decine = '';
    $numero = '';
    if (4 == strlen($cifra) or 1 == strlen($cifra)) $cifra = '0' . $cifra;
    switch (strlen($cifra)):
        case 6:
            $numero = left($cifra, 1);
            $cifra = substr($cifra, 1);
            switch ($numero) {
                case 0:
                    $centinaiaDiMigliaia = '';
                    break;
                case 1:
                    $centinaiaDiMigliaia = 'cento';
                    break;
                default:
                    $centinaiaDiMigliaia = $arrayCifreSingole[$numero] . 'cento';
                    break;
            }
        case 5:
            $numero = left($cifra, 1);
            $cifra = substr($cifra, 1);
        case 4:
            $numero .= left($cifra, 1);
            $cifra = substr($cifra, 1);
            $numero = intval($numero);
            switch ($numero) {
                case 0:
                    $migliaia = '';
                    break;
                case 1:
                    $migliaia = 'mille';
                    break;
                case 1 < $numero and $numero < 20:
                    $migliaia = $arrayCifreSingole[$numero] . 'mila';
                    break;
                case 79 < $numero and $numero < 90:
                    if ($centinaiaDiMigliaia) $centinaiaDiMigliaia = left($centinaiaDiMigliaia, (strlen($centinaiaDiMigliaia) - 1));
                default:
                    $unitario = $numero % 10;
                    $decina = intval($numero / 10);
                    $migliaia = $arrayCifreDecimi[$decina];
                    if (1 == $unitario) $migliaia = left($migliaia, (strlen($migliaia) - 1)) . 'uno';
                    else if (8 == $unitario) $migliaia = left($migliaia, (strlen($migliaia) - 1)) . 'otto';
                    else if (0 != $unitario) $migliaia .= $arrayCifreSingole[$unitario];
                    $migliaia .= 'mila';
                    break;
            }
            if ($centinaiaDiMigliaia) {
                if ('mille' == $migliaia) $migliaia = left($centinaiaDiMigliaia, (strlen($centinaiaDiMigliaia) - 1)) . 'unomila';
                else if ('' == $migliaia) $migliaia = $centinaiaDiMigliaia . 'mila';
                else {
                    $migliaia = $centinaiaDiMigliaia . $migliaia;
                }
            }
        case 3:
            $numero = left($cifra, 1);
            $cifra = substr($cifra, 1);
            switch ($numero) {
                case 0:
                    $centinaia = '';
                    break;
                case 1:
                    $centinaia = 'cento';
                    break;
                default:
                    $centinaia = $arrayCifreSingole[$numero] . 'cento';
                    break;
            }
        case 2:
            $numero = left($cifra, 1);
            $cifra = substr($cifra, 1);
        case 1:
            $numero .= left($cifra, 1);
            $cifra = substr($cifra, 1);
            $numero = intval($numero);
            switch ($numero) {
                case 0:
                    $decine = '';
                    break;
                case 1:
                    $decine = 'uno';
                    break;
                case 1 < $numero and $numero < 20:
                    $decine = $arrayCifreSingole[$numero];
                    break;
                case 79 < $numero and $numero < 90:
                    if ($centinaia) $centinaia = left($centinaia, (strlen($centinaia) - 1));
                default:
                    $unitario = $numero % 10;
                    $decina = intval($numero / 10);
                    $decine = $arrayCifreDecimi[$decina];
                    if (1 == $unitario) $decine = left($decine, (strlen($decine) - 1)) . 'uno';
                    else if (8 == $unitario) $decine = left($decine, (strlen($decine) - 1)) . 'otto';
                    else if (0 != $unitario) $decine .= $arrayCifreSingole[$unitario];
                    break;
            }

    endswitch;
    $strInLettere = $migliaia . $centinaia . $decine;
    if (!$strInLettere) $strInLettere = 'zero';

    $strInLettere .= '/' . str_pad($decimali, 2, 0, STR_PAD_RIGHT);

    return strtoupper($segno . $strInLettere);
}

function strFileMaxSize()
{
    $max_upload = (int)(ini_get('upload_max_filesize'));
    $max_upload = round($max_upload * 1000000 / (1024 * 1024), 2);

    return $max_upload . "MB";
}

function directoryNamePerUpload($directory)
{
    docRoot(ROOT_FOLDER);
    $docRoot = DOC_ROOT . '/upload/';
    if (!is_dir($docRoot)) mkdir($docRoot, 0777);
    $salt = sha1(md5(SALT));
    $directory = md5($directory . $salt);

    $directory = $docRoot . $directory . '/';
    if (!is_dir($directory)) mkdir($directory, 0777);

    return $directory;
}

function directoryNameHtml($directory)
{
    $docRoot = ROOT_FOLDER . '/upload/';
    $salt = sha1(md5(SALT));
    $directory = md5($directory . $salt);

    return $docRoot . $directory . '/';
}

function hashCartella($directory)
{
    $salt = sha1(md5(SALT));
    $directory = md5($directory . $salt);

    return $directory;
}

function alternaRighe(&$riga, $classe = 'alternata')
{
    if ($riga) echo ' class="' . $classe . '"';
    $riga = !$riga;
}

function controllaFormatoOra($ora)
{
    $pattern = "/^\d{2}([.:-])\d{2}$/";

    return preg_match($pattern, $ora);
}

function controllaFormatoData($data)
{
    $pattern = "/^\d{2}([./-])\d{2}\1\d{4}$/";

    return preg_match($pattern, $data);
}

/**
 * @param        $stringa
 * @param        $lunghezza
 * @param string $carattere
 * @param string $direzione
 *
 * @return string
 */
function completaStringa($stringa, $lunghezza, $carattere = " ", $direzione = "S")
{
    switch (strtoupper($direzione)) {
        case 'S':
            $direzione = STR_PAD_LEFT;
            break;
        case 'D':
            $direzione = STR_PAD_RIGHT;
            break;
        case 'O':
            $direzione = STR_PAD_BOTH;
            break;
        case STR_PAD_BOTH:
        case STR_PAD_LEFT:
        case STR_PAD_RIGHT:
            break;
        default:
            $direzione = STR_PAD_LEFT;
            break;
    }

    return str_pad($stringa, $lunghezza, $carattere, $direzione);
}

/**
 * @param PDO $pdo
 * @param     $databaseSchema1
 * @param     $databaseSchema2
 */
function confrontaDatabases($pdo, $databaseSchema1, $databaseSchema2)
{
    $boolConnessione = false;
    if (!$pdo) {
        $pdo = connettiPdo();
        $boolConnessione = true;
    }
    echo 'Confronto tra ' . $databaseSchema1 . ' e ' . $databaseSchema2 . '<br/>';
    $prepareCiclaTabelle = $pdo->prepare('select T.TABLE_NAME from information_schema.TABLES T where T.TABLE_SCHEMA=? and T.TABLE_TYPE = "BASE TABLE"');
    $prepareCiclaColonne = $pdo->prepare('select C.COLUMN_NAME AS Nome,C.COLUMN_TYPE AS Tipo from information_schema.COLUMNS C where C.TABLE_SCHEMA=? and C.TABLE_NAME = ?');
    if ($boolConnessione) $pdo = null;
    $blobTabelle = queryPreparedPdo($pdo, $prepareCiclaTabelle, array($databaseSchema1), 'p');
    while ($tabella = $blobTabelle->fetchColumn()) {
        $arrayColonneDB1 = array();
        $arrayColonneDB2 = array();
        $blobColonne = queryPreparedPdo($pdo, $prepareCiclaColonne, array($databaseSchema1, $tabella), 'p');
        while ($colonna = $blobColonne->fetch()) {
            $arrayColonneDB1[$colonna['Nome']] = $colonna['Tipo'];
        }
        $blobColonne->closeCursor();
        $blobColonne = queryPreparedPdo($pdo, $prepareCiclaColonne, array($databaseSchema2, $tabella), 'p');
        while ($colonna = $blobColonne->fetch()) {
            $arrayColonneDB2[$colonna['Nome']] = $colonna['Tipo'];
        }
        $blobColonne->closeCursor();
        if ($arrayColonneDB1 == $arrayColonneDB2) {
            //echo 'Tabella '.$tabella.' identica in struttura tra i database.<br/>';
            continue;
        }
        $differenzeTraPrimoSecondo = array_diff_assoc($arrayColonneDB1, $arrayColonneDB2);
        $differenzeTraSecondoPrimo = array_diff_assoc($arrayColonneDB2, $arrayColonneDB1);
        $differenze = array_merge($differenzeTraPrimoSecondo, $differenzeTraSecondoPrimo);

        if (!$differenze) {
            continue;
        }
        echo '<hr/><b style="background-color:yellow">Tabella ' . $tabella . '</b><hr/>';
        echo '<ol>';
        foreach ($differenze as $campo => $valore) {
            echo '<li>Campo <b style="color:blue">' . $campo . '</b>: ';
            echo 'Su <b>' . $databaseSchema1 . '</b> ';
            if (!isset($arrayColonneDB1[$campo])) {

                echo ' <font style="color:red">non &egrave; stato trovato;</font>';
            }
            else {
                echo ' &egrave; di contratti <font style="color:red">' . $arrayColonneDB1[$campo] . '</font>';
            }
            echo ' Su <b>' . $databaseSchema2 . '</b> ';
            if (!isset($arrayColonneDB2[$campo])) {

                echo ' <font style="color:red">non &egrave; stato trovato;</font>';
            }
            else {
                echo ' &egrave; di contratti <font style="color:red">' . $arrayColonneDB2[$campo] . '</font>';
            }
            echo '</li>' . PHP_EOL;
        }
        echo '</ol>';
    }
    $blobTabelle->closeCursor();
}

function aggiungiPostIt($strNota, $strCollegamento = '[?]', $strID = null)
{
    ?><span title="<?php echo $strNota; ?>" <?php if ($strID) echo "id='$strID'" ?> style="cursor: help"
            class="tooltip"><?php echo $strCollegamento ?></span><?php
}

/**
 * La funzione mostra un'immagine di un lucchetto aperto o chiuso in base al bool passato
 *
 * @param bool $boolAperto
 * @param null $strID
 * @param bool $boolHide
 *
 * @return string
 */
function addLucchetto($boolAperto = false, $strID = null, $boolHide = false)
{
    $str = ROOT_FOLDER . '/grafica/' . ($boolAperto ? 'lock_closed20.png' : 'lock_open20.png');
    $strPerDisplay = '';
    if ($strID) {
        $strPerDisplay = ($boolHide) ? 'none' : 'inline';
        $strPerDisplay = ' id="' . $strID . '" style="display:' . $strPerDisplay . '"';
    }

    return '<img src="' . $str . '" ' . $strPerDisplay . ' />';
}

/**
 * La funzione verifica se la variabile passata e' un oggetto e se appartiene alla classe indicata (default PDO)
 *
 * @param object $obj
 * @param string $strKind
 *
 * @return bool
 */
function isIstance($obj, $strKind = 'PDO')
{
    if (!is_object($obj)) {
        return false;
    }

    return is_a($obj, $strKind);
}

function isLoggedIn()
{
    if (isset($_SESSION['user_logged_in'])) {
        if ($_SESSION['user_logged_in'] == 1) {
            return true;
        }
    }

    return false;
}


function creaGuid($stringa)
{
    $stringa = strtolower($stringa);
    $stringa = str_replace(' ', '-', $stringa);
    $stringa = str_replace('!', '', $stringa);
    $stringa = str_replace('à', 'a', $stringa);
    $stringa = str_replace('è', 'e', $stringa);
    $stringa = str_replace('é', 'e', $stringa);
    $stringa = str_replace('ì', 'i', $stringa);
    $stringa = str_replace('ò', 'o', $stringa);
    $stringa = str_replace('ù', 'u', $stringa);

    return $stringa;
}


function valutaGetPostSession($valore, $metodo = '')
{
    switch ($metodo) {
        case 'GET':
            if (isset($_GET[$valore]))
                return $_GET[$valore];
            else return null;
        case 'POST':
            if (isset($_POST[$valore]))
                return $_POST[$valore];
            else return null;
        case 'SESSION':
            if (isset($_SESSION[$valore]))
                return $_SESSION[$valore];
            else return null;
        default:
            if (isset($_POST[$valore]))
                return $_POST[$valore];
            elseif (isset($_GET[$valore]))
                return $_GET[$valore];
            elseif (isset($_SESSION[$valore]))
                return $_SESSION[$valore];
            else return null;
    }
}

function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}

function creaUserName($user)
{
    $user = str_replace(array(' ', '.', '"', "'"), '', $user);

    return strtolower($user);
}

?>

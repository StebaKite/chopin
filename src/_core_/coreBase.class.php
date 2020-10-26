<?php

//require_once 'nexus6.abstract.class.php';

abstract class CoreBase {

    const NULL_VALUE = "null";

    function isEmpty($param) {
        if (($param == "") or ( $param == " ") or ( $param == null)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function isNotEmpty($param) {
        if (($param != "") and ( $param != " ") and ( $param != null)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function quotation($param) {
        return "'" . str_replace("'", "''", $param) . "'";
    }
    
    public static function quotationAllNegozi() {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        return "'" . str_replace(",", "','", $array['negozi']) . "'";
    }

    public function getInfoFromServer($infoName) {        
        if (null !== filter_input(INPUT_SERVER, $infoName)) {
            return filter_input(INPUT_SERVER, $infoName);            
        }
        return null;
    }
    
    public static function getIndexSession($indexName) {    
        return (isset($_SESSION[$indexName])) ? $_SESSION[$indexName] : null;
    }
    
    public static function setIndexSession($indexName, $indexValue) {
        $_SESSION[$indexName] = $indexValue;
    }
    
    public static function unsetIndexSessione($indexName) {
        unset($indexName);
    }
    
}
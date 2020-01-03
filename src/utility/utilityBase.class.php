<?php

/**
 * Description of utilityBase
 *
 * @author BarbieriStefano
 */
class UtilityBase {
        
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

<?php

require_once 'utility.component.interface.php';
require_once 'utilityBase.class.php';

class Utility extends UtilityBase implements UtilityComponentInterface {

    private $root;
    private static $languageReplace;
    private static $configuration;
    private static $configFile = "/chopin/config/chopin.config.ini";

    // Setters -----------------------
    public function setLanguageReplace($languageReplace) {
        self::$languageReplace = $languageReplace;
    }

    public function setConfiguration($configuration) {
        self::$configuration = $configuration;
    }

    // Getters -----------------------
    public function getConfiguration() {
        return self::$configuration;
    }

    public function getLanguageReplace() {
        return self::$languageReplace;
    }

    // Costruttore --------------------
    function __construct() {
        $this->root = parent::getIndexSession('DOCUMENT_ROOT');
    }

    private function __autoload($class_name) {
        require_once $class_name . 'class.php';
    }

    private function __clone() {

    }

    /**
     * Singleton Pattern
     */
    public static function getInstance() {
        if (parent::getIndexSession("Obj_utility") === NULL) {
            parent::setIndexSession("Obj_utility", serialize(new Utility()));
        }
        return unserialize(parent::getIndexSession("Obj_utility"));
    }

    public function tailFile($template, $replacement) {
        return str_replace(array_keys($replacement), array_values($replacement), $template);
    }

    public function tailTemplate($template) {

        $array = $this->getConfig();

        $lingua = $array['language'];
        $lanFile = "languageFile_" . $lingua;

        $fileLingua = parent::getInfoFromServer('DOCUMENT_ROOT') . $array[$lanFile];

        /*
         * Se non trova il file corrispondente alla lingua impostata, il metodo
         * restituisce il template così com'è senza traduzioni
         */
        if (file_exists($fileLingua)) {
            return $this->tailFile($template, $this->getMultilanguageFile($fileLingua));
        } else {
            error_log("Multilanguage file " . $fileLingua . " not found, template use");
            return $template;
        }
    }

    public function getTemplate($fileName) {

        if (file_exists($fileName)) {

            $temp = fopen($fileName, "r");
            $template = fread($temp, filesize($fileName));
            fclose($temp);

            return $this->tailTemplate($template);
        } else {
            error_log("Template file " . $fileName . " not found!");
        }
    }

    public function getQueryTemplate($fileName) {

        if (file_exists($fileName)) {

            $temp = fopen($fileName, "r");
            $template = fread($temp, filesize($fileName));
            fclose($temp);

            return $template;
        } else {
            error_log("Template file " . $fileName . " not found!");
        }
    }

    /*
     * Prende in input il file della lingua e restituisce una array associativa
     */

    private function getMultilanguageFile($multiLanguageFile) {

        try {
            if (self::$languageReplace == "") {

                $languageReplace = array();

                $lan = fopen($multiLanguageFile, "r");

                while (!feof($lan)) {
                    $line = explode(" = ", fgets($lan));
                    if (trim($line[0]) != "") {
                        if (substr($line[0], 0, 1) != ";") {
                            $key = "%ml." . trim($line[0]) . "%";
                            $value = trim($line[1]);
                            $languageReplace[$key] = $value;
                        }
                    }
                }
                fclose($lan);
                $this->setLanguageReplace($languageReplace);
            }
            return $this->getLanguageReplace();
        } catch (Throwable $e) {
            error_log(">>>" + $e->getMessage() + "<<<");
            throw $e;
        };
    }

    public function getConfig() {

        if (self::$configuration == "") {

            $configFile = $_SERVER['DOCUMENT_ROOT'] . self::$configFile;

            if (file_exists($configFile)) {

                // viene ritornata una mappa
                $this->setConfiguration(parse_ini_file($configFile));
            } else {
                error_log("Config file " . $configFile . " not found!");
                $this->setConfiguration(null);
            }
        }
        return $this->getConfiguration();
    }

    public function getConfigInBatchMode($project_root) {

        if (self::$configuration == "") {

            $configFile = $project_root . self::$configFile;

            if (file_exists($configFile)) {

                // viene ritornata una mappa
                $this->setConfiguration(parse_ini_file($configFile));
            } else {
                error_log("Config file " . $configFile . " not found!");
                $this->setConfiguration(null);
            }
        }
        return $this->getConfiguration();
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
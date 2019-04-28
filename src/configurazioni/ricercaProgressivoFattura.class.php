<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'ricercaProgressivoFattura.template.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'progressivoFattura.class.php';

class RicercaProgressivoFattura extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();

        $this->testata = $this->root . $this->array[self::TESTATA];
        $this->piede = $this->root . $this->array[self::PIEDE];
        $this->messaggioErrore = $this->root . $this->array[self::ERRORE];
        $this->messaggioInfo = $this->root . $this->array[self::INFO];
    }

    public function getInstance() {
        if (!isset($_SESSION[self::RICERCA_PROGRESSIVO_FATTURA]))
            $_SESSION[self::RICERCA_PROGRESSIVO_FATTURA] = serialize(new RicercaProgressivoFattura());
        return unserialize($_SESSION[self::RICERCA_PROGRESSIVO_FATTURA]);
    }

    public function start() {
        $this->go();
    }

    public function go() {
        $progressivoFattura = ProgressivoFattura::getInstance();
        $db = Database::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $ricercaProgressivoFatturaTemplate = RicercaProgressivoFatturaTemplate::getInstance();
        $this->preparaPagina($ricercaProgressivoFatturaTemplate);

        $replace = (isset($_SESSION[self::AMBIENTE]) ? array('%amb%' => $_SESSION[self::AMBIENTE], '%users%' => $_SESSION[self::USERS], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array, $_SESSION), '%menu%' => $this->makeMenu($utility)));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);

        if ($this->refreshProgressiviFattura($db, $progressivoFattura)) {

            $_SESSION[self::MESSAGGIO] = "Trovati " . $progressivoFattura->getQtaProgressiviFattura() . " progressivi fattura";

            self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
            $template = $utility->tailFile($utility->getTemplate($this->messaggioInfo), self::$replace);
            $_SESSION[self::MSG] = $utility->tailTemplate($template);
        } else {

            $_SESSION[self::MESSAGGIO] = self::ERRORE_LETTURA;

            self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
            $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
            echo $utility->tailTemplate($template);
        }

        $ricercaProgressivoFatturaTemplate->displayPagina();

        include($this->piede);
    }

    private function refreshProgressiviFattura($db, $progressivoFattura) {

        if (!$progressivoFattura->load($db)) {
            $_SESSION[self::MESSAGGIO] = self::ERRORE_LETTURA;
            return false;
        }
        return true;
    }

    public function preparaPagina($ricercaProgressivoFatturaTemplate) {

        $_SESSION[self::AZIONE] = self::AZIONE_RICERCA_PROGRESSIVO_FATTURA;
        $_SESSION[self::TITOLO_PAGINA] = "%ml.ricercaProgressivoFattura%";
    }

}

?>
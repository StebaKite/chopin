<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'ricercaProgressivoFattura.template.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'progressivoFattura.class.php';

class RicercaProgressivoFattura extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();

        $this->testata = $this->root . $this->array[self::TESTATA];
        $this->piede = $this->root . $this->array[self::PIEDE];
        $this->messaggioErrore = $this->root . $this->array[self::ERRORE];
        $this->messaggioInfo = $this->root . $this->array[self::INFO];
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::RICERCA_PROGRESSIVO_FATTURA) === NULL) {
            parent::setIndexSession(self::RICERCA_PROGRESSIVO_FATTURA, serialize(new RicercaProgressivoFattura()));
        }
        return unserialize(parent::getIndexSession(self::RICERCA_PROGRESSIVO_FATTURA));
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

        $replace = parent::getIndexSession(self::AMBIENTE) !== NULL ? array('%amb%' => parent::getIndexSession(self::AMBIENTE), '%users%' => parent::getIndexSession(self::USERS), '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array), '%menu%' => $this->makeMenu($utility));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);

        if ($this->refreshProgressiviFattura($db, $progressivoFattura)) {

            parent::setIndexSession(self::MESSAGGIO, "Trovati " . $progressivoFattura->getQtaProgressiviFattura() . " progressivi fattura");

            self::$replace = array('%messaggio%' => parent::getIndexSession(self::MESSAGGIO));
            $template = $utility->tailFile($utility->getTemplate($this->messaggioInfo), self::$replace);
            parent::setIndexSession(self::MSG, $utility->tailTemplate($template));
        } else {

            parent::setIndexSession(self::MESSAGGIO, self::ERRORE_LETTURA);

            self::$replace = array('%messaggio%' => parent::getIndexSession(self::MESSAGGIO));
            $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
            echo $utility->tailTemplate($template);
        }

        $ricercaProgressivoFatturaTemplate->displayPagina();

        include($this->piede);
    }

    private function refreshProgressiviFattura($db, $progressivoFattura) {

        if (!$progressivoFattura->load($db)) {
            parent::setIndexSession(self::MESSAGGIO, self::ERRORE_LETTURA);
            return false;
        }
        return true;
    }

    public function preparaPagina($ricercaProgressivoFatturaTemplate) {

        parent::setIndexSession(self::AZIONE, self::AZIONE_RICERCA_PROGRESSIVO_FATTURA);
        parent::setIndexSession(self::TITOLO_PAGINA, "%ml.ricercaProgressivoFattura%");
    }

}
<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.presentation.interface.php';
require_once 'utility.class.php';
require_once 'fornitore.class.php';

class CreaFornitoreTemplate extends AnagraficaAbstract implements AnagraficaPresentationInterface {

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
        if (parent::getIndexSession(self::CREA_FORNITORE_TEMPLATE) === NULL) {
            parent::setIndexSession(self::CREA_FORNITORE_TEMPLATE, serialize(new CreaFornitoreTemplate()));
        }
        return unserialize(parent::getIndexSession(self::CREA_FORNITORE_TEMPLATE));
    }

    // template ------------------------------------------------

    public function inizializzaPagina() {
        
    }

    public function controlliLogici() {

        $fornitore = Fornitore::getInstance();

        $esito = TRUE;
        $msg = "<br>";

        /**
         * Controllo presenza dati obbligatori
         */
        if ($fornitore->getCodFornitore() == "") {
            $msg = $msg . self::ERRORE_CODICE_FORNITORE;
            $esito = FALSE;
        }

        if ($fornitore->getDesFornitore() == "") {
            $msg = $msg . self::ERRORE_DESCRIZIONE_FORNITORE;
            $esito = FALSE;
        }

        // ----------------------------------------------

        if ($msg != "<br>") {
            parent::setIndexSession(self::MESSAGGIO, $msg);
        } else {
            parent::unsetIndexSessione(self::MESSAGGIO);
        }
        return $esito;
    }

    public function displayPagina() {

        $form = $this->root . $this->array['template'] . self::PAGINA_CREA_FORNITORE;

        $fornitore = Fornitore::getInstance();
        $replace = array(
            '%titoloPagina%' => $this->getTitoloPagina(),
            '%azione%' => $this->getAzione(),
            '%confermaTip%' => $this->getConfermaTip(),
            '%codfornitore%' => $fornitore->getCodFornitore(),
            '%desfornitore%' => $fornitore->getDesFornitore(),
            '%indfornitore%' => $fornitore->getDesIndirizzoFornitore(),
            '%cittafornitore%' => $fornitore->getDesCittaFornitore(),
            '%capfornitore%' => $fornitore->getCapFornitore(),
            '%tipoaddebito%' => $fornitore->getTipAddebito()
        );

        $template = $this->utility->tailFile($this->utility->getTemplate($form), $replace);
        echo $this->utility->tailTemplate($template);
    }

}
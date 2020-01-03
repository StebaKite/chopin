<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'ricercaCausale.template.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'causale.class.php';

class RicercaCausale extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

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
        if (parent::getIndexSession(self::RICERCA_CAUSALE) === NULL) {
            parent::setIndexSession(self::RICERCA_CAUSALE, serialize(new RicercaCausale()));
        }
        return unserialize(parent::getIndexSession(self::RICERCA_CAUSALE));
    }

    public function start() {
        $this->go();
    }

    public function go() {

        $causale = Causale::getInstance();
        $db = Database::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $ricercaCausaleTemplate = RicercaCausaleTemplate::getInstance();
        $this->preparaPagina($ricercaCausaleTemplate);

        $replace = parent::getIndexSession(self::AMBIENTE) !== NULL ? array('%amb%' => parent::getIndexSession(self::AMBIENTE), '%users%' => parent::getIndexSession(self::USERS), '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array), '%menu%' => $this->makeMenu($utility));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);

        if ($this->refreshCausali($db, $causale)) {
            parent::setIndexSession(self::MESSAGGIO, "Trovate " . $causale->getQtaCausali() . " causali");

            self::$replace = array('%messaggio%' => parent::getIndexSession(self::MESSAGGIO));
            $template = $utility->tailFile($utility->getTemplate($this->messaggioInfo), self::$replace);
            parent::setIndexSession(self::MSG, $utility->tailTemplate($template));
        } else {

            parent::setIndexSession(self::MESSAGGIO, self::ERRORE_LETTURA);

            self::$replace = array('%messaggio%' => parent::getIndexSession(self::MESSAGGIO));
            $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
            echo $utility->tailTemplate($template);
        }

        $ricercaCausaleTemplate->displayPagina();

        include($this->piede);
    }

    /**
     * Questo metodo osserva il contenuto dell'array causali dell'oggetto. Se è vuoto lo ricarica e
     * ri-serializza l'oggetto in sessione, se è pieno non fa nulla e lascia l'array esistente
     */
    private function refreshCausali($db, $causale) {

        if ($causale->getQtaCausali() == 0) {

            if (!$causale->load($db)) {
                parent::setIndexSession(self::MESSAGGIO, self::ERRORE_LETTURA);
                return false;
            }
            parent::setIndexSession(self::CAUSALE, serialize($causale));
        }
        return true;
    }

    public function preparaPagina($ricercaCausaleTemplate) {

        parent::setIndexSession(self::AZIONE, self::AZIONE_RICERCA_CAUSALE);
        parent::setIndexSession(self::TITOLO_PAGINA, "%ml.ricercaCausale%");
    }

}
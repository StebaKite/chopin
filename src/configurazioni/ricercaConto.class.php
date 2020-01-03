<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'ricercaConto.template.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'conto.class.php';

class RicercaConto extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

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
        if (parent::getIndexSession(self::RICERCA_CONTO) === NULL) {
            parent::setIndexSession(self::RICERCA_CONTO, serialize(new RicercaConto()));
        }
        return unserialize(parent::getIndexSession(self::RICERCA_CONTO));
    }

    public function start() {

        $conto = Conto::getInstance();
        $sottoconto = Sottoconto::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $conto->setConti(null);
        $sottoconto->preparaNuoviSottoconti();
        parent::setIndexSession(self::SOTTOCONTO, serialize($sottoconto));

        $ricercaContoTemplate = RicercaContoTemplate::getInstance();
        $this->preparaPagina($ricercaContoTemplate);

        $replace = parent::getIndexSession(self::AMBIENTE) !== NULL ? array('%amb%' => parent::getIndexSession(self::AMBIENTE), '%users%' => parent::getIndexSession(self::USERS), '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array), '%menu%' => $this->makeMenu($utility));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);

        $ricercaContoTemplate->displayPagina();
        include($this->piede);
    }

    public function go() {

        $conto = Conto::getInstance();
        $db = Database::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $ricercaContoTemplate = RicercaContoTemplate::getInstance();
        $this->preparaPagina($ricercaContoTemplate);

        $replace = parent::getIndexSession(self::AMBIENTE) !== NULL ? array('%amb%' => parent::getIndexSession(self::AMBIENTE), '%users%' => parent::getIndexSession(self::USERS), '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array), '%menu%' => $this->makeMenu($utility));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);

        if ($conto->load($db)) {

            parent::setIndexSession(self::CONTO, serialize($conto));
            parent::setIndexSession(self::MESSAGGIO, "Trovati " . $conto->getQtaConti() . " conti");

            self::$replace = array('%messaggio%' => parent::getIndexSession(self::MESSAGGIO));
            $template = $utility->tailFile($utility->getTemplate($this->messaggioInfo), self::$replace);
            parent::setIndexSession(self::MSG, $utility->tailTemplate($template));
        } else {

            parent::setIndexSession(self::MESSAGGIO, self::ERRORE_LETTURA);

            self::$replace = array('%messaggio%' => parent::getIndexSession(self::MESSAGGIO));
            $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
            echo $utility->tailTemplate($template);
        }
        $ricercaContoTemplate->displayPagina();

        include($this->piede);
    }

    public function preparaPagina($ricercaContoTemplate) {

        parent::setIndexSession(self::AZIONE, self::AZIONE_RICERCA_CONTO);
        parent::setIndexSession(self::TIP_CONFERMA, "%ml.cercaTip%");
        parent::setIndexSession(self::TITOLO_PAGINA, "%ml.ricercaConto%");
    }

}
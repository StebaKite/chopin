<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.business.interface.php';
require_once 'ricercaMercato.template.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'mercato.class.php';

class RicercaMercato extends AnagraficaAbstract implements AnagraficaBusinessInterface {

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
        if (parent::getIndexSession(self::RICERCA_MERCATO) === NULL) {
            parent::setIndexSession(self::RICERCA_MERCATO, serialize(new RicercaMercato()));
        }
        return unserialize(parent::getIndexSession(self::RICERCA_MERCATO));
    }

    public function start() {

        // Template

        $mercato = Mercato::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $ricercaMercatoTemplate = RicercaMercatoTemplate::getInstance();

        $this->preparaPagina($ricercaMercatoTemplate);

        $replace = parent::getIndexSession(self::AMBIENTE) !== NULL ? array('%amb%' => parent::getIndexSession(self::AMBIENTE), '%users%' => parent::getIndexSession(self::USERS), '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array), '%menu%' => $this->makeMenu($utility));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);

        $mercato->setMercati(null);

        if ($mercato->load(Database::getInstance())) {

            parent::setIndexSession(self::MERCATO, serialize($mercato));
            parent::setIndexSession(self::MESSAGGIO, "Trovati " . $mercato->getQtaMercati() . " mercati");
            self::$replace = array('%messaggio%' => parent::getIndexSession(self::MESSAGGIO));

            $pos = strpos(parent::getIndexSession(self::MESSAGGIO), "ERRORE");
            if ($pos === false) {
                if ($mercato->getQtaMercati() > 0) {
                    $template = $utility->tailFile($utility->getTemplate($this->messaggioInfo), self::$replace);
                } else {
                    $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
                }
            } else {
                $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
            }
            parent::setIndexSession(self::MSG, $utility->tailTemplate($template));
        }
        else {
            parent::setIndexSession(self::MESSAGGIO, self::ERRORE_LETTURA);
            self::$replace = array('%messaggio%' => parent::getIndexSession(self::MESSAGGIO));
            $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
            echo $utility->tailTemplate($template);
        }
        $ricercaMercatoTemplate->displayPagina();

        include($this->piede);
    }

    public function go() {
        $this->start();
    }

    private function preparaPagina() {

        parent::setIndexSession(self::AZIONE, self::AZIONE_RICERCA_MERCATO);
        parent::setIndexSession(self::TIP_CONFERMA, "%ml.cercaTip%");
        parent::setIndexSession(self::TITOLO_PAGINA, "%ml.ricercaMercato%");
    }

}
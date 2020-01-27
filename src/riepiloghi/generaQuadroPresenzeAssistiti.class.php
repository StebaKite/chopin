<?php

require_once 'riepiloghi.abstract.class.php';
require_once 'riepiloghi.business.interface.php';
require_once 'generaQuadroPresenzeAssistiti.template.php';
require_once 'utility.class.php';
require_once 'presenzaAssistito.class.php';
require_once 'database.class.php';

class GeneraQuadroPresenzeAssistiti extends RiepiloghiAbstract implements RiepiloghiBusinessInterface {

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
        if (parent::getIndexSession(self::GENERA_QUADRO_PRESENZE_ASSISTITI) === NULL) {
            parent::setIndexSession(self::GENERA_QUADRO_PRESENZE_ASSISTITI, serialize(new GeneraQuadroPresenzeAssistiti()));
        }
        return unserialize(parent::getIndexSession(self::GENERA_QUADRO_PRESENZE_ASSISTITI));
    }

    public function go() {

        $totAssistiti = 0;
        
        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $db = Database::getInstance();
        $presenzaAssistito = PresenzaAssistito::getInstance();

        $presenzeAssistitoTemplate = GeneraQuadroPresenzeAssistitiTemplate::getInstance();
        $this->preparaPagina($presenzeAssistitoTemplate);

        $replace = parent::getIndexSession(self::AMBIENTE) === NULL ? array('%amb%' => parent::getIndexSession(self::AMBIENTE), '%users%' => parent::getIndexSession(self::USERS), '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array), '%menu%' => $this->makeMenu($utility));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);

        $presenzaAssistito->prepara();
        $presenzaAssistito->ricercaPresenze($db);

        $totAssistiti = $presenzaAssistito->getNumPresenze();
        parent::setIndexSession(self::MESSAGGIO, "Trovate " . $totAssistiti . " voci");
        self::$replace = array('%messaggio%' => parent::getIndexSession(self::MESSAGGIO));
        
        if ($totAssistiti > 0) {
            $template = $utility->tailFile($utility->getTemplate($this->messaggioInfo), parent::$replace);
        } else {
            $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), parent::$replace);
        }
        parent::setIndexSession(self::MSG, $utility->tailTemplate($template));
        $presenzeAssistitoTemplate->displayPagina();
        include($this->piede);
    }

    public function start() {
        
        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $presenzaAssistito = PresenzaAssistito::getInstance();

        $presenzaAssistito->prepara();
        
        parent::unsetIndexSessione(self::MESSAGGIO);
        parent::unsetIndexSessione(self::MSG);

        $presenzeAssistitoTemplate = GeneraQuadroPresenzeAssistitiTemplate::getInstance();
        $this->preparaPagina($presenzeAssistitoTemplate);

        $replace = parent::getIndexSession(self::AMBIENTE) === NULL ? array('%amb%' => parent::getIndexSession(self::AMBIENTE), '%users%' => parent::getIndexSession(self::USERS), '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array), '%menu%' => $this->makeMenu($utility));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);

        $presenzeAssistitoTemplate->displayPagina();
        include($this->piede);
        
    }

    private function preparaPagina($presenzeAssistitoTemplate) {

        parent::setIndexSession(self::AZIONE, self::AZIONE_PRESENZE_ASSISTITO);
        parent::setIndexSession(self::TITOLO_PAGINA, "%ml.quadroPresenzeAssistiti%");
    }

}
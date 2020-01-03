<?php

require_once 'nexus6.abstract.class.php';
require_once 'main.presentation.interface.php';
require_once 'utility.class.php';

class MainTemplate extends Nexus6Abstract implements MainPresentationInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();

        $this->testata = $this->root . $this->array['testataPagina'];
        $this->piede = $this->root . $this->array['piedePagina'];
        $this->messaggioErrore = $this->root . $this->array['messaggioErrore'];
        $this->messaggioInfo = $this->root . $this->array['messaggioInfo'];
    }

    public static function getInstance() {
        if (parent::getIndexSession("Obj_maintemplate") === NULL) {
            parent::setIndexSession("Obj_maintemplate", serialize(new MainTemplate()));
        }
        return unserialize(parent::getIndexSession("Obj_maintemplate"));
    }

    public function controlliLogici() {
        
    }

    public function displayPagina() {

        // Template --------------------------------------------------------------

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $form = $this->root . $array['template'] . self::MAIN_PAGE;

        $this->getEnvironment($array);

        // Pagina -----------------------------------------------------

        $replace = array(
            '%messaggio%' => parent::getIndexSession(self::MESSAGGIO),
            '%menu%' => $this->makeMenu($utility),
            '%amb%' => parent::getIndexSession(self::AMBIENTE),
            '%users%' => parent::getIndexSession(self::USERS),
        );

        parent::unsetIndexSessione('avvisoDialog');
        parent::unsetIndexSessione('avvisoDiv');

        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);

        $template = $utility->tailFile($utility->getTemplate($form), $replace);
        echo $utility->tailTemplate($template);

        include($this->piede);
    }

}

?>
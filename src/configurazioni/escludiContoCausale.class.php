<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'configuraCausale.class.php';

class EscludiContoCausale extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::ESCLUDI_CONTO_CAUSALE) === NULL) {
            parent::setIndexSession(self::ESCLUDI_CONTO_CAUSALE, serialize(new EscludiContoCausale()));
        }
        return unserialize(parent::getIndexSession(self::ESCLUDI_CONTO_CAUSALE));
    }

    public function start() {
        $configurazioneCausale = ConfigurazioneCausale::getInstance();
        $utility = Utility::getInstance();
        $db = Database::getInstance();
        $array = $utility->getConfig();

        $configurazioneCausale->cancellaConto($db);
        parent::setIndexSession(self::CONFIGURAZIONE_CAUSALE, serialize($configurazioneCausale));

        $risultato_xml = $this->root . $array['template'] . self::XML_CAUSALE;

        $replace = array(
            '%conticonfigurati%' => trim($this->makeTableContiConfigurati($configurazioneCausale)),
            '%contidisponibili%' => trim($this->makeTableContiConfigurabili($configurazioneCausale))
        );
        $template = $utility->tailFile($utility->getTemplate($risultato_xml), $replace);
        echo $utility->tailTemplate($template);
    }

    public function go() {
        
    }

}
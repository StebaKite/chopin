<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'configuraCausale.class.php';
require_once 'configurazioneCausale.class.php';

class ConfiguraCausale extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::CONFIGURA_CAUSALE) === NULL) {
            parent::setIndexSession(self::CONFIGURA_CAUSALE, serialize(new ConfiguraCausale()));
        }
        return unserialize(parent::getIndexSession(self::CONFIGURA_CAUSALE));
    }

    public function start() {
        $configurazioneCausale = ConfigurazioneCausale::getInstance();
        $db = Database::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $this->refreshContiConfigurati($db, $configurazioneCausale);
        $this->refreshContiConfigurabili($db, $configurazioneCausale);

        $risultato_xml = $this->root . $array['template'] . self::XML_CAUSALE;

        $replace = array(
            '%conticonfigurati%' => trim($this->makeTableContiConfigurati($configurazioneCausale)),
            '%contidisponibili%' => trim($this->makeTableContiConfigurabili($configurazioneCausale))
        );
        $template = $utility->tailFile($utility->getTemplate($risultato_xml), $replace);
        echo $utility->tailTemplate($template);
    }

    public function go() {}

}
<?php

require_once 'scadenze.abstract.class.php';
require_once 'scadenze.business.interface.php';
require_once 'scadenze.controller.class.php';
require_once 'ricercaScadenzeCliente.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'scadenzaCliente.class.php';
require_once 'registrazione.class.php';

class VisualizzaScadenzaCliente extends ScadenzeAbstract implements ScadenzeBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::VISUALIZZA_SCADENZA_CLIENTE) === NULL) {
            parent::setIndexSession(self::VISUALIZZA_SCADENZA_CLIENTE, serialize(new VisualizzaScadenzaCliente()));
        }
        return unserialize(parent::getIndexSession(self::VISUALIZZA_SCADENZA_CLIENTE));
    }

    public function start() {
        $scadenza = ScadenzaCliente::getInstance();
        $registrazione = Registrazione::getInstance();
        $db = Database::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $scadenza->leggi($db);

        $registrazione->setIdRegistrazione($scadenza->getIdRegistrazione());
        $registrazione->leggi($db);
        $registrazioneOriginante = $this->makeTabellaReadOnlyRegistrazioneOriginale($registrazione);

        if (trim($scadenza->getStaScadenza()) == self::SCADENZA_CHIUSA) {
            $registrazione->setIdRegistrazione($scadenza->getIdIncasso());
            $registrazione->leggi($db);
            $incasso = $this->makeTabellaReadOnlyIncasso($registrazione);
        } else
            $incasso = "<tbody><tr><td class='bg-warning'>Questa scadenza non ha ancora un incasso associato</td></tr></tbody>";

        $risultato_xml = $this->root . $array['template'] . self::XML_SCADENZA_CLIENTE;

        $replace = array(
            '%data%' => trim($scadenza->getDatRegistrazione()),
            '%importo%' => trim($scadenza->getImpRegistrazione()),
            '%addebito%' => trim($scadenza->getTipAddebito()),
            '%negozio%' => trim($scadenza->getCodNegozio()),
            '%stato%' => trim($scadenza->getStaScadenza()),
            '%fattura%' => trim($scadenza->getNumFattura()),
            '%nota%' => trim($scadenza->getNota()),
            '%registrazioneoriginante%' => $registrazioneOriginante,
            '%incasso%' => $incasso
        );
        $template = $utility->tailFile($utility->getTemplate($risultato_xml), $replace);
        echo $utility->tailTemplate($template);
    }

    public function go() {

    }

}

?>
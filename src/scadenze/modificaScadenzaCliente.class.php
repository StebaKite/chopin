<?php

require_once 'scadenze.abstract.class.php';
require_once 'scadenze.business.interface.php';
require_once 'scadenze.controller.class.php';
require_once 'ricercaScadenzeCliente.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'scadenzaCliente.class.php';
require_once 'registrazione.class.php';

class ModificaScadenzaCliente extends ScadenzeAbstract implements ScadenzeBusinessInterface {

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();
    }

    public function getInstance() {
        if (!isset($_SESSION[self::MODIFICA_SCADENZA_CLIENTE]))
            $_SESSION[self::MODIFICA_SCADENZA_CLIENTE] = serialize(new ModificaScadenzaCliente());
        return unserialize($_SESSION[self::MODIFICA_SCADENZA_CLIENTE]);
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
        $registrazioneOriginante = $this->makeTabellaRegistrazioneOriginale($registrazione);

        if (trim($scadenza->getStaScadenza()) == self::SCADENZA_CHIUSA) {
            $registrazione->setIdRegistrazione($scadenza->getIdIncasso());
            $registrazione->leggi($db);
            $incasso = $this->makeTabellaIncasso($registrazione);
        } else
            $incasso = "<tbody><tr><td class='bg-warning'>Questa scadenza non ha ancora un incasso associato</td></tr></tbody>";

        $risultato_xml = $this->root . $array['template'] . self::XML_SCADENZA_CLIENTE;

        $replace = array(
            '%cliente%' => trim($scadenza->getIdCliente()),
            '%data%' => trim($scadenza->getDatRegistrazione()),
            '%importo%' => trim($scadenza->getImpRegistrazione()),
            '%addebito%' => trim($scadenza->getTipAddebito()),
            '%stato%' => trim($scadenza->getStaScadenza()),
            '%fattura%' => trim($scadenza->getNumFattura()),
            '%nota%' => trim($scadenza->getNota()),
            '%negozio%' => trim($scadenza->getCodNegozio()),
            '%registrazioneoriginante%' => $registrazioneOriginante,
            '%incasso%' => $incasso
        );
        $template = $utility->tailFile($utility->getTemplate($risultato_xml), $replace);
        echo $utility->tailTemplate($template);
    }

    public function go() {
        $scadenza = ScadenzaCliente::getInstance();
        $db = Database::getInstance();

        $scadenza->aggiorna($db);

        $_SESSION[self::SCADENZE_CONTROLLER] = serialize(new ScadenzeController(RicercaScadenzeCliente::getInstance()));
        $controller = unserialize($_SESSION[self::SCADENZE_CONTROLLER]);
        $controller->start();
    }

}

?>
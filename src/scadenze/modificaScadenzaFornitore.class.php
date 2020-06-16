<?php

require_once 'scadenze.abstract.class.php';
require_once 'scadenze.business.interface.php';
require_once 'scadenze.controller.class.php';
require_once 'ricercaScadenzeFornitore.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'scadenzaFornitore.class.php';
require_once 'registrazione.class.php';

class ModificaScadenzaFornitore extends ScadenzeAbstract implements ScadenzeBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::MODIFICA_SCADENZA_FORNITORE) === NULL) {
            parent::setIndexSession(self::MODIFICA_SCADENZA_FORNITORE, serialize(new ModificaScadenzaFornitore()));
        }
        return unserialize(parent::getIndexSession(self::MODIFICA_SCADENZA_FORNITORE));
    }

    public function start() {
        $scadenza = ScadenzaFornitore::getInstance();
        $registrazione = Registrazione::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $db = Database::getInstance();

        $scadenza->leggi($db);

        $registrazione->setIdRegistrazione($scadenza->getIdRegistrazione());
        $registrazione->leggi($db);
        $registrazioneOriginante = $this->makeTabellaRegistrazioneOriginale($registrazione);

        if (trim($scadenza->getStaScadenza()) == self::SCADENZA_CHIUSA) {
            $registrazione->setIdRegistrazione($scadenza->getIdPagamento());
            $registrazione->leggi($db);
            $pagamento = $this->makeTabellaPagamento($registrazione);
        } else
            $pagamento = "<tbody>" .
                    "	<tr><td class='bg-warning'>Questa scadenza non ha ancora un pagamento associato</td></tr>" .
                    "</tbody>";


        $risultato_xml = $this->root . $array['template'] . self::XML_SCADENZA_FORNITORE;

        $replace = array(
            '%data%' => trim($scadenza->getDatScadenza()),
            '%importo%' => trim($scadenza->getImpInScadenza()),
            '%addebito%' => trim($scadenza->getTipAddebito()),
            '%fornitore%' => str_replace("&", "&amp;", trim($registrazione->getIdFornitore())),
            '%negozio%' => trim($scadenza->getCodNegozio()),
            '%stato%' => trim($scadenza->getStaScadenza()),
            '%fattura%' => trim($scadenza->getNumFattura()),
            '%nota%' => str_replace("&", "&amp;", trim($scadenza->getNotaScadenza())),
            '%registrazioneoriginante%' => $registrazioneOriginante,
            '%pagamento%' => $pagamento
        );
        $template = $utility->tailFile($utility->getTemplate($risultato_xml), $replace);
        echo $utility->tailTemplate($template);
    }

    public function go() {
        $scadenza = ScadenzaFornitore::getInstance();
        $registrazione = Registrazione::getInstance();
        $db = Database::getInstance();

        $scadenza->setIdFornitore($registrazione->getIdFornitore());
        $scadenza->aggiorna($db);

        parent::setIndexSession(self::SCADENZE_CONTROLLER, serialize(new ScadenzeController(RicercaScadenzeFornitore::getInstance())));
        $controller = unserialize(parent::getIndexSession(self::SCADENZE_CONTROLLER));
        $controller->start();
    }

}

?>
<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'fornitore.class.php';
require_once 'scadenzaFornitore.class.php';

class RimuoviFatturaPagata extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {

        $this->root = $_SERVER['DOCUMENT_ROOT'];
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::RIMUOVI_FATTURA_PAGATA])) {
            $_SESSION[self::RIMUOVI_FATTURA_PAGATA] = serialize(new RimuoviFatturaPagata());
        }
        return unserialize($_SESSION[self::RIMUOVI_FATTURA_PAGATA]);
    }

    public function start() {
        $this->go();
    }

    public function go() {
        $db = Database::getInstance();
        $registrazione = Registrazione::getInstance();
        $scadenzaFornitore = ScadenzaFornitore::getInstance();
        $fornitore = Fornitore::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $fornitore->cercaConDescrizione($db);
        $scadenzaFornitore->setIdFornitore($fornitore->getIdFornitore());

        if ($scadenzaFornitore->getIdTableScadenzeAperte() == "scadenze_aperte_pag_cre") {
            $scadenzaFornitore->leggi($db);
            $scadenzaFornitore->rimuoviScadenzaPagata();

            $risultato_xml = $this->root . $array['template'] . self::XML_SCADENZE_FORNITORE_APERTE;

            $replace = array(
                '%scadenzedapagare%' => $this->makeTabellaFattureDaPagare($scadenzaFornitore),
                '%scadenzepagate%' => $this->makeTabellaFatturePagate($scadenzaFornitore)
            );
            $template = $utility->tailFile($utility->getTemplate($risultato_xml), $replace);
            echo $utility->tailTemplate($template);
        } elseif ($scadenzaFornitore->getIdTableScadenzeAperte() == "scadenze_aperte_pag_mod") {
            $scadenzaFornitore->setIdFornitore($registrazione->getIdFornitore());
            $scadenzaFornitore->setStaScadenza("00");   // aperta e da pagare
            $scadenzaFornitore->setIdPagamento("");
            $scadenzaFornitore->cambiaStatoScadenza($db);
            $scadenzaFornitore->trovaScadenzeDaPagare($db);
            $scadenzaFornitore->trovaScadenzePagate($db);

            $risultato_xml = $this->root . $array['template'] . self::XML_SCADENZE_FORNITORE_APERTE;

            $replace = array(
                '%scadenzedapagare%' => $this->makeTabellaFattureDaPagare($scadenzaFornitore),
                '%scadenzepagate%' => $this->makeTabellaFatturePagate($scadenzaFornitore)
            );
            $template = $utility->tailFile($utility->getTemplate($risultato_xml), $replace);
            echo $utility->tailTemplate($template);
        }
    }

}
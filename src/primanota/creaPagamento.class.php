<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'primanota.controller.class.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'registrazione.class.php';
require_once 'dettaglioRegistrazione.class.php';
require_once 'causale.class.php';
require_once 'ricercaRegistrazione.class.php';
require_once 'scadenzaFornitore.class.php';
require_once 'fornitore.class.php';
require_once 'lavoroPianificato.class.php';

class CreaPagamento extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::CREA_PAGAMENTO])) {
            $_SESSION[self::CREA_PAGAMENTO] = serialize(new CreaPagamento());
        }
        return unserialize($_SESSION[self::CREA_PAGAMENTO]);
    }

    public function start() {
        $registragione = Registrazione::getInstance();
        $registragione->prepara();

        $fornitore = Fornitore::getInstance();
        $fornitore->prepara();

        $scadenzaFornitore = ScadenzaFornitore::getInstance();
        $scadenzaFornitore->setQtaScadenzeDaPagare(0);
        $scadenzaFornitore->setScadenzeDaPagare("");
        $scadenzaFornitore->setQtaScadenzePagate(0);
        $scadenzaFornitore->setScadenzePagate("");
        $scadenzaFornitore->setIdTableScadenzeAperte("scadenze_aperte_pag_cre");
        $scadenzaFornitore->setIdTableScadenzeChiuse("scadenze_chiuse_pag_cre");

        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $dettaglioRegistrazione->prepara();
        $dettaglioRegistrazione->setIdTablePagina("dettagli_pag_cre");

        $_SESSION[self::DETTAGLIO_REGISTRAZIONE] = serialize($dettaglioRegistrazione);
        $_SESSION[self::SCADENZA_FORNITORE] = serialize($scadenzaFornitore);
        echo "Ok";
    }

    public function go() {
        $registrazione = Registrazione::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $utility = Utility::getInstance();

        $this->creaPagamento($utility, $registrazione, $dettaglioRegistrazione);

        $_SESSION["Obj_primanotacontroller"] = serialize(new PrimanotaController(RicercaRegistrazione::getInstance()));
        $controller = unserialize($_SESSION["Obj_primanotacontroller"]);
        $controller->start();
    }

    public function creaPagamento($utility, $registrazione, $dettaglioRegistrazione) {
        $scadenzaFornitore = ScadenzaFornitore::getInstance();
        $fornitore = Fornitore::getInstance();
        $db = Database::getInstance();
        $db->beginTransaction();

        $registrazione->setNumFattura(trim($scadenzaFornitore->getNumFattura()));       // numero della fattura in scadenza
        $registrazione->setCodNegozio(trim($scadenzaFornitore->getCodNegozio()));       // lo stesso negozio della fattura in scadenza

        if ($registrazione->inserisci($db)) {
            foreach ($dettaglioRegistrazione->getDettagliRegistrazione() as $unDettaglio) {
                $this->creaDettaglioPagamento($db, $utility, $registrazione, $dettaglioRegistrazione, $unDettaglio);
            }

            /**
             * Riconciliazione delle fatture indicate con chiusura delle rispettive scadenze
             */
            $riconciliazioneFattureOkay = true;

            foreach ($scadenzaFornitore->getScadenzePagate() as $unaScadenza) {
                $scadenzaFornitore->setIdFornitore($fornitore->getIdFornitore());
                $scadenzaFornitore->setIdPagamento($registrazione->getIdRegistrazione());
                $scadenzaFornitore->setIdScadenza($unaScadenza[ScadenzaFornitore::ID_SCADENZA]);
                $scadenzaFornitore->setStaScadenza("10");  // pagata e chiusa

                $scadenzaFornitore->setNumFattura($unaScadenza[ScadenzaFornitore::NUM_FATTURA]);
                $scadenzaFornitore->setDatScadenza($unaScadenza[ScadenzaFornitore::DAT_SCADENZA]);

                if (!$scadenzaFornitore->cambiaStatoScadenza($db)) {
                    $riconciliazioneFattureOkay = false;
                    break;
                }
            }

            /*             * *
             * Ricalcolo i saldi dei conti
             */
            if ($riconciliazioneFattureOkay) {
                $this->ricalcolaSaldi($db, $registrazione->getDatRegistrazione());
                $db->commitTransaction();
                return true;
            } else {
                $db->rollbackTransaction();
                return false;
            }
        } else {
            $db->rollbackTransaction();
            return false;
        }
    }

    public function creaDettaglioPagamento($db, $utility, $registrazione, $dettaglioRegistrazione, $unDettaglio) {
        $_cc = explode(" - ", $unDettaglio[DettaglioRegistrazione::COD_CONTO]); // il codconto del dettaglio contiene anche la descrizione
        $conto = explode(".", $_cc[0]);  // conto e sottoconto separati da un punto

        $dettaglioRegistrazione->setIdRegistrazione($registrazione->getIdRegistrazione());
        $dettaglioRegistrazione->setCodConto($conto[0]);
        $dettaglioRegistrazione->setCodSottoconto($conto[1]);
        $dettaglioRegistrazione->setImpRegistrazione($unDettaglio[DettaglioRegistrazione::IMP_REGISTRAZIONE]);
        $dettaglioRegistrazione->setIndDareavere($unDettaglio[DettaglioRegistrazione::IND_DAREAVERE]);

        if (!$dettaglioRegistrazione->inserisci($db)) {
            $db->rollbackTransaction();
            return false;
        }
        return true;
    }

}
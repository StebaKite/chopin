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
require_once 'scadenzaCliente.class.php';
require_once 'cliente.class.php';
require_once 'lavoroPianificato.class.php';

class CreaIncasso extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::CREA_INCASSO) === NULL) {
            parent::setIndexSession(self::CREA_INCASSO, serialize(new CreaIncasso()));
        }
        return unserialize(parent::getIndexSession(self::CREA_INCASSO));
    }

    public function start() {
        $registragione = Registrazione::getInstance();
        $registragione->prepara();

        $cliente = Cliente::getInstance();
        $cliente->prepara();

        $scadenzaCliente = ScadenzaCliente::getInstance();
        $scadenzaCliente->setQtaScadenzeDaIncassare(0);
        $scadenzaCliente->setScadenzeDaIncassare("");
        $scadenzaCliente->setQtaScadenzeIncassate(0);
        $scadenzaCliente->setScadenzeIncassate("");
        $scadenzaCliente->setIdTableScadenzeAperte("scadenze_aperte_inc_cre");
        $scadenzaCliente->setIdTableScadenzeChiuse("scadenze_chiuse_inc_cre");

        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $dettaglioRegistrazione->prepara();
        $dettaglioRegistrazione->setIdTablePagina("dettagli_inc_cre");

        parent::setIndexSession(self::DETTAGLIO_REGISTRAZIONE, serialize($dettaglioRegistrazione));
        parent::setIndexSession(self::SCADENZA_CLIENTE, serialize($scadenzaCliente));

        echo "Ok";
    }

    public function go() {
        $registrazione = Registrazione::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $utility = Utility::getInstance();

        $this->creaIncasso($utility, $registrazione, $dettaglioRegistrazione);

        parent::setIndexSession("Obj_primanotacontroller", serialize(new PrimanotaController(RicercaRegistrazione::getInstance())));
        $controller = unserialize(parent::getIndexSession("Obj_primanotacontroller"));
        $controller->start();
    }

    public function creaIncasso($utility, $registrazione, $dettaglioRegistrazione) {
        $scadenzaCliente = ScadenzaCliente::getInstance();
        $cliente = Cliente::getInstance();
        $db = Database::getInstance();
        $db->beginTransaction();

        if ($registrazione->inserisci($db)) {
            foreach ($dettaglioRegistrazione->getDettagliRegistrazione() as $unDettaglio) {
                $this->creaDettaglioIncasso($db, $utility, $registrazione, $dettaglioRegistrazione, $unDettaglio);
            }

            /**
             * Riconciliazione delle fatture indicate con chiusura delle rispettive scadenze
             */
            $riconciliazioneFattureOkay = true;

            foreach ($scadenzaCliente->getScadenzeIncassate() as $unaScadenza) {
                $scadenzaCliente->setIdCliente($cliente->getIdCliente());
                $scadenzaCliente->setIdIncasso($registrazione->getIdRegistrazione());
                $scadenzaCliente->setIdScadenza($unaScadenza[ScadenzaCliente::ID_SCADENZA]);
                $scadenzaCliente->setStaScadenza("10");  // incassata e chiusa

                $scadenzaCliente->setNumFattura($unaScadenza[ScadenzaCliente::NUM_FATTURA]);
                $scadenzaCliente->setDatRegistrazione($unaScadenza[ScadenzaCliente::DAT_REGISTRAZIONE]);

                if (!$scadenzaCliente->cambiaStato($db)) {
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

    public function creaDettaglioIncasso($db, $utility, $registrazione, $dettaglioRegistrazione, $unDettaglio) {
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
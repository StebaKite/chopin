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
require_once 'scadenzaCliente.class.php';
require_once 'fornitore.class.php';
require_once 'cliente.class.php';
require_once 'lavoroPianificato.class.php';

class CreaRegistrazione extends primanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::CREA_REGISTRAZIONE) === NULL) {
            parent::setIndexSession(self::CREA_REGISTRAZIONE, serialize(new CreaRegistrazione()));
        }
        return unserialize(parent::getIndexSession(self::CREA_REGISTRAZIONE));
    }

    public function start() {
        $registragione = Registrazione::getInstance();
        $registragione->prepara();

        $cliente = Cliente::getInstance();
        $cliente->prepara();

        $fornitore = Fornitore::getInstance();
        $fornitore->prepara();

        $scadenzaFornitore = ScadenzaFornitore::getInstance();
        $scadenzaFornitore->setQtaScadenzeDaPagare(0);
        $scadenzaFornitore->setScadenzeDaPagare("");
        $scadenzaFornitore->setIdTableScadenzeAperte("scadenzesuppl_cre");

        $scadenzaCliente = ScadenzaCliente::getInstance();
        $scadenzaCliente->setQtaScadenzeDaIncassare(0);
        $scadenzaCliente->setScadenzeDaIncassare("");
        $scadenzaCliente->setIdTableScadenzeAperte("scadenzesuppl_cre");

        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $dettaglioRegistrazione->prepara();
        $dettaglioRegistrazione->setIdTablePagina("dettagli_cre");

        parent::setIndexSession(self::DETTAGLIO_REGISTRAZIONE, serialize($dettaglioRegistrazione));
        parent::setIndexSession(self::SCADENZA_FORNITORE, serialize($scadenzaFornitore));
        parent::setIndexSession(self::SCADENZA_CLIENTE, serialize($scadenzaCliente));

        echo "Ok";
    }

    public function go() {
        $registrazione = Registrazione::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $utility = Utility::getInstance();

        $this->creaRegistrazione($utility, $registrazione, $dettaglioRegistrazione);

        parent::setIndexSession("Obj_primanotacontroller", serialize(new PrimanotaController(RicercaRegistrazione::getInstance())));
        $controller = unserialize(parent::getIndexSession("Obj_primanotacontroller"));
        $controller->start();
    }

    public function creaRegistrazione($utility, $registrazione, $dettaglioRegistrazione) {
        $scadenzaFornitore = ScadenzaFornitore::getInstance();
        $scadenzaCliente = ScadenzaCliente::getInstance();
        $db = Database::getInstance();
        $db->beginTransaction();
        $dettagli_ok = true;

        if ($registrazione->inserisci($db)) {

            if ($dettaglioRegistrazione->getQtaDettagliRegistrazione() > 0) {                
                foreach ($dettaglioRegistrazione->getDettagliRegistrazione() as $unDettaglio) {
                    if ($this->creaDettaglioRegistrazione($db, $utility, $registrazione, $dettaglioRegistrazione, $unDettaglio)) {
                    } else {
                        $dettagli_ok = false;
                        break;
                    }
                }
            }

            /**
             * Inserisco le eventuali scadenze del fornitore o del cliente
             */
            if ($dettagli_ok) {
                if (parent::isNotEmpty($registrazione->getIdFornitore())) {
                    if ($scadenzaFornitore->getQtaScadenzeDaPagare() > 0) {
                        if (!$this->creaScadenzeFornitore($utility, $db, $registrazione, $dettaglioRegistrazione, $scadenzaFornitore)) {
                            $db->rollbackTransaction();
                            return false;
                        }
                    }
                } else {
                    if (parent::isNotEmpty($registrazione->getIdCliente())) {
                        if ($scadenzaCliente->getQtaScadenzeDaIncassare() > 0) {
                            if (!$this->creaScadenzeCliente($utility, $db, $registrazione, $dettaglioRegistrazione, $scadenzaCliente)) {
                                $db->rollbackTransaction();
                                return false;
                            }
                        }
                    }
                }

                /**
                 * Ricalcolo i saldi dei conti
                 */
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

    public function creaDettaglioRegistrazione($db, $utility, $registrazione, $dettaglioRegistrazione, $unDettaglio) {
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

    public function creaScadenzeFornitore($utility, $db, $registrazione, $dettaglioRegistrazione, $scadenzaFornitore) {
        $fornitore = Fornitore::getInstance();
        $fornitore->setIdFornitore($registrazione->getIdFornitore());
        $fornitore->leggi($db);
        $array = $utility->getConfig();

        $scadenzaFornitore->setIdRegistrazione($registrazione->getIdRegistrazione());
        $scadenzaFornitore->setTipAddebito($fornitore->getTipAddebito());
        $scadenzaFornitore->setStaScadenza("00");
        $scadenzaFornitore->setIdFornitore($fornitore->getIdFornitore());
        $scadenzaFornitore->setNotaScadenza($registrazione->getDesRegistrazione());
        $scadenzaFornitore->setCodNegozio($registrazione->getCodNegozio());
        $scadenzaFornitore->setNumFattura($registrazione->getNumFattura());

        /**
         * Inserisco tutte le scadenza aggiunte nella tabella
         */
        if ($scadenzaFornitore->getQtaScadenzeDaPagare() > 0) {
//			$data1 = str_replace("'", "", $registrazione->getDatRegistrazione());					// la datareg arriva con gli apici per il db
            $dataRegistrazione = strtotime($registrazione->getDatRegistrazione());

            foreach ($scadenzaFornitore->getScadenzeDaPagare() as $unaScadenza) {
//				$data = str_replace("'", "", $unaScadenza[ScadenzaFornitore::DAT_SCADENZA]);			// la datascad arriva con gli apici per il db
                $dataScadenza = strtotime($unaScadenza[ScadenzaFornitore::DAT_SCADENZA]);       // cambio i separatori altrimenti la strtotime non funziona

                if ($dataScadenza > $dataRegistrazione) {
                    /**
                     *  se la registrazione è una nota di accredito (causale 1110) inverte il segno dell'importo in modo che venga sottratto al totale
                     *  parziale della data in scadenza
                     */
                    $importo_in_scadenza = (strstr($array['notaDiAccredito'], $registrazione->getCodCausale())) ? $unaScadenza[ScadenzaFornitore::IMP_IN_SCADENZA] * (-1) : $unaScadenza[ScadenzaFornitore::IMP_IN_SCADENZA];
                    $scadenzaFornitore->setImpInScadenza($importo_in_scadenza);
                    $scadenzaFornitore->setDatScadenza($unaScadenza[ScadenzaFornitore::DAT_SCADENZA]);
                    $scadenzaFornitore->setNumFattura($registrazione->getNumFattura());

                    if (!$scadenzaFornitore->inserisci($db)) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    public function creaScadenzeCliente($utility, $db, $registrazione, $dettaglioRegistrazione, $scadenzaCliente) {
        $cliente = Cliente::getInstance();
        $cliente->setIdCliente($registrazione->getIdCliente());
        $cliente->leggi($db);

        $scadenzaCliente->setIdRegistrazione($registrazione->getIdRegistrazione());
        $scadenzaCliente->setTipAddebito($cliente->getTipAddebito());
        $scadenzaCliente->setStaScadenza("00");
        $scadenzaCliente->setIdCliente($cliente->getIdCliente());
        $scadenzaCliente->setNota($registrazione->getDesRegistrazione());
        $scadenzaCliente->setCodNegozio($registrazione->getCodNegozio());
        $scadenzaCliente->setNumFattura($registrazione->getNumFattura());

        /**
         * Inserisco tutte le scadenza aggiunte nella tabella
         */
        if ($scadenzaCliente->getQtaScadenzeDaIncassare() > 0) {
            $data1 = str_replace("'", "", $registrazione->getDatRegistrazione());     // la datareg arriva con gli apici per il db
            $dataRegistrazione = strtotime(str_replace('/', '-', $data1));

            foreach ($scadenzaCliente->getScadenzeDaIncassare() as $unaScadenza) {
                $data = str_replace("'", "", $unaScadenza[ScadenzaCliente::DAT_REGISTRAZIONE]);   // la datascad arriva con gli apici per il db
                $dataScadenza = strtotime(str_replace('/', '-', $data));       // cambio i separatori altrimenti la strtotime non funziona

                if ($dataScadenza > $dataRegistrazione) {
                    $scadenzaCliente->setImpRegistrazione($unaScadenza[ScadenzaCliente::IMP_REGISTRAZIONE]);
                    $scadenzaCliente->setDatRegistrazione($unaScadenza[ScadenzaCliente::DAT_REGISTRAZIONE]);
                    $scadenzaCliente->setNumFattura($registrazione->getNumFattura());

                    if (!$scadenzaCliente->inserisci($db)) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

}
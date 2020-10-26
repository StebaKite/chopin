<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'scadenze.controller.class.php';
require_once 'ricercaRegistrazione.class.php';
require_once 'ricercaScadenzeFornitore.class.php';
require_once 'ricercaScadenzeCliente.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'registrazione.class.php';
require_once 'fornitore.class.php';
require_once 'cliente.class.php';
require_once 'scadenzaFornitore.class.php';
require_once 'scadenzaCliente.class.php';
require_once 'causale.class.php';

class ModificaRegistrazione extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::MODIFICA_REGISTRAZIONE) === null) {
            parent::setIndexSession(self::MODIFICA_REGISTRAZIONE, serialize(new ModificaRegistrazione()));
        }
        return unserialize(parent::getIndexSession(self::MODIFICA_REGISTRAZIONE));
    }

    public function start() {
        $registrazione = Registrazione::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $fornitore = Fornitore::getInstance();
        $cliente = Cliente::getInstance();
        $scadenzaFornitore = ScadenzaFornitore::getInstance();
        $scadenzaCliente = ScadenzaCliente::getInstance();
        $causale = Causale::getInstance();

        $utility = Utility::getInstance();
        $db = Database::getInstance();
        $array = $utility->getConfig();

        $registrazione->prepara();
        $dettaglioRegistrazione->prepara();
        $cliente->prepara();
        $fornitore->prepara();

        $registrazione->leggi($db);
        parent::setIndexSession(self::REGISTRAZIONE, serialize($registrazione));

        if (parent::isNotEmpty($registrazione->getIdFornitore())) {
            $fornitore->setIdFornitore($registrazione->getIdFornitore());
            $fornitore->leggi($db);
            $scadenzaFornitore->setIdRegistrazione($registrazione->getIdRegistrazione());
            $scadenzaFornitore->trovaScadenzeRegistrazione($db);
            $scadenzaFornitore->setIdTableScadenzeAperte("scadenzesuppl_mod");
            parent::setIndexSession(self::SCADENZA_FORNITORE, serialize($scadenzaFornitore));
        }

        if (parent::isNotEmpty($registrazione->getIdCliente())) {
            $cliente->setIdCliente($registrazione->getIdCliente());
            $cliente->leggi($db);
            $scadenzaCliente->setIdRegistrazione($registrazione->getIdRegistrazione());
            $scadenzaCliente->trovaScadenzeRegistrazione($db);
            $scadenzaCliente->setIdTableScadenzeAperte("scadenzesuppl_mod");
            parent::setIndexSession(self::SCADENZA_CLIENTE, serialize($scadenzaCliente));
        }

        $dettaglioRegistrazione->setIdRegistrazione($registrazione->getIdRegistrazione());
        $dettaglioRegistrazione->leggiDettagliRegistrazione($db);
        $dettaglioRegistrazione->setCampoMsgControlloPagina("tddettagli_mod");
        $dettaglioRegistrazione->setIdTablePagina("dettagli_mod");
        $dettaglioRegistrazione->setMsgControlloPagina("messaggioControlloDettagli_mod");
        $dettaglioRegistrazione->setNomeCampo("descreg_mod");
        $dettaglioRegistrazione->setLabelNomeCampo("descreg_mod_label");
        parent::setIndexSession(self::DETTAGLIO_REGISTRAZIONE, serialize($dettaglioRegistrazione));

        $causale->setCodCausale($registrazione->getCodCausale());

        $risultato_xml = $this->root . $array['template'] . self::XML_MODIFICA_REGISTRAZIONE;

        $replace = array(
            '%datareg%' => trim($registrazione->getDatRegistrazione()),
            '%descreg%' => str_replace("&", "&amp;", trim($registrazione->getDesRegistrazione())),
            '%causale%' => trim($registrazione->getCodCausale()),
            '%codneg%' => trim($registrazione->getCodNegozio()),
            '%fornitore%' => str_replace("&", "&amp;", trim($fornitore->getDesFornitore())),
            '%cliente%' => str_replace("&", "&amp;", trim($cliente->getDesCliente())),
            '%idfornitore%' => trim($fornitore->getIdFornitore()),
            '%idcliente%' => trim($cliente->getIdCliente()),
            '%numfatt%' => trim($registrazione->getNumFattura()),
            '%numfattorig%' => trim($registrazione->getNumFatturaOrig()),
            '%scadenzesupplfornitore%' => trim($this->makeTabellaScadenzeFornitore($scadenzaFornitore,$dettaglioRegistrazione)),
            '%scadenzesupplcliente%' => trim($this->makeTabellaScadenzeCliente($scadenzaCliente,$dettaglioRegistrazione)),
            '%dettagli%' => trim($this->makeTabellaDettagliRegistrazione($registrazione, $dettaglioRegistrazione, $scadenzaFornitore, $scadenzaCliente)),
            '%conti%' => trim($causale->loadContiConfigurati($db))
        );
        $template = $utility->tailFile($utility->getTemplate($risultato_xml), $replace);
        echo $utility->tailTemplate($template);
    }

    public function go() {
        $registrazione = Registrazione::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $fornitore = Fornitore::getInstance();
        $cliente = Cliente::getInstance();
        $scadenzaFornitore = ScadenzaFornitore::getInstance();
        $scadenzaCliente = ScadenzaCliente::getInstance();
        $causale = Causale::getInstance();
        $utility = Utility::getInstance();

        $this->aggiornaRegistrazione($utility, $registrazione, $dettaglioRegistrazione, $scadenzaFornitore, $scadenzaCliente, $fornitore, $cliente);

        /**
         * Passo il controllo al controller del componente di provenienza
         */
        if (parent::getIndexSession(self::FUNCTION_REFERER) !== null) {

            if (parent::getIndexSession(self::FUNCTION_REFERER) == self::RICERCA_SCADENZE_FORNITORE) {
                parent::setIndexSession(self::SCADENZE_CONTROLLER, serialize(new ScadenzeController(RicercaScadenzeFornitore::getInstance())));
                $controller = unserialize(parent::getIndexSession(self::SCADENZE_CONTROLLER));
            } elseif (parent::getIndexSession(self::FUNCTION_REFERER) == self::RICERCA_SCADENZE_CLIENTE) {
                parent::setIndexSession(self::SCADENZE_CONTROLLER, serialize(new ScadenzeController(RicercaScadenzeCliente::getInstance())));
                $controller = unserialize(parent::getIndexSession(self::SCADENZE_CONTROLLER));
            }
        } else {
            parent::setIndexSession(self::PRIMANOTA_CONTROLLER, serialize(new PrimanotaController(RicercaRegistrazione::getInstance())));
            $controller = unserialize(parent::getIndexSession(self::PRIMANOTA_CONTROLLER));
        }
        $controller->start();
    }

    public function aggiornaRegistrazione($utility, $registrazione, $dettaglioRegistrazione, $scadenzaFornitore, $scadenzaCliente, $fornitore, $cliente) {
        $db = Database::getInstance();
        $db->beginTransaction();

        if ($registrazione->aggiorna($db)) {
            if ($this->aggiornaDettagli($db, $utility, $registrazione, $dettaglioRegistrazione)) {
                /*
                 * Tutto ok, Aggiorno le scadenze fornitore o cliente
                 *
                 * - inserisco quelle aggiunte
                 * - aggiorno quelle esistenti con i dati variati dell'operazione
                 */

                if (parent::isNotEmpty($registrazione->getIdFornitore())) {
                    if ($this->aggiornaScadenzeFornitore($db, $utility, $registrazione, $scadenzaFornitore, $fornitore)) {
                        
                    }  // tutto ok
                    else {
                        $db->rollbackTransaction();
                        return false;
                    }
                } else {
                    if (parent::isNotEmpty($registrazione->getIdCliente())) {
                        if ($this->aggiornaScadenzeCliente($db, $utility, $registrazione, $scadenzaCliente, $cliente)) {
                            
                        }  // tutto ok
                        else {
                            $db->rollbackTransaction();
                            return false;
                        }
                    }
                }
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

    private function aggiornaScadenzeFornitore($db, $utility, $registrazione, $scadenzaFornitore, $fornitore) {
        $array = $utility->getConfig();

        foreach ($scadenzaFornitore->getScadenzeDaPagare() as $unaScadenza) {
            $scadenzaFornitore->setIdFornitoreOrig($unaScadenza[ScadenzaFornitore::ID_FORNITORE]);
            $scadenzaFornitore->setIdFornitore($registrazione->getIdFornitore());
            $scadenzaFornitore->setIdRegistrazione($registrazione->getIdRegistrazione());
            $scadenzaFornitore->setDatScadenza($unaScadenza[ScadenzaFornitore::DAT_SCADENZA]);
            $scadenzaFornitore->setDatScadenzaNuova($unaScadenza[ScadenzaFornitore::DAT_SCADENZA]);

            /**
             *  se la registrazione è una nota di accredito (causale 1110) inverte il segno dell'importo in scadenza
             */
            $importo_in_scadenza = (strstr($array['notaDiAccredito'], $registrazione->getCodCausale())) ? $unaScadenza[ScadenzaFornitore::IMP_IN_SCADENZA] * (-1) : $unaScadenza[ScadenzaFornitore::IMP_IN_SCADENZA];

            $scadenzaFornitore->setImpInScadenza($importo_in_scadenza);
            $scadenzaFornitore->setNotaScadenza($registrazione->getDesRegistrazione());
            $scadenzaFornitore->setTipAddebito($unaScadenza[ScadenzaFornitore::TIP_ADDEBITO]);
            $scadenzaFornitore->setCodNegozio($registrazione->getCodNegozio());
            $scadenzaFornitore->setIdFornitore($registrazione->getIdFornitore());
            $scadenzaFornitore->setNumFattura($registrazione->getNumFattura());
            $scadenzaFornitore->setNumFatturaOrig($unaScadenza[ScadenzaFornitore::NUM_FATTURA]);
            $scadenzaFornitore->setStaScadenza($unaScadenza[ScadenzaFornitore::STA_SCADENZA]);

            if ($unaScadenza[ScadenzaFornitore::ID_SCADENZA] == 0) {
                $scadenzaFornitore->setStaScadenza(self::SCADENZA_APERTA);
                if ($scadenzaFornitore->inserisci($db)) {
                    
                } // tutto ok
                else
                    return false;
            }
            else {
                if ($scadenzaFornitore->aggiorna($db)) {
                    
                } // tutto ok
                else
                    return false;
            }
        }
        return true;
    }

    private function aggiornaScadenzeCliente($db, $utility, $registrazione, $scadenzaCliente, $cliente) {
        $array = $utility->getConfig();

        foreach ($scadenzaCliente->getScadenzeDaIncassare() as $unaScadenza) {
            $scadenzaCliente->setIdClienteOrig($unaScadenza[ScadenzaCliente::ID_CLIENTE]);
            $scadenzaCliente->setIdCliente($registrazione->getIdCliente());
            $scadenzaCliente->setIdRegistrazione($registrazione->getIdRegistrazione());
            $scadenzaCliente->setDatRegistrazione($unaScadenza[ScadenzaCliente::DAT_REGISTRAZIONE]);
            $scadenzaCliente->setDatScadenzaNuova($unaScadenza[ScadenzaCliente::DAT_REGISTRAZIONE]);

            $scadenzaCliente->setImpRegistrazione($unaScadenza[ScadenzaCliente::IMP_REGISTRAZIONE]);
            $scadenzaCliente->setNota($registrazione->getDesRegistrazione());
            $scadenzaCliente->setTipAddebito($unaScadenza[ScadenzaCliente::TIP_ADDEBITO]);
            $scadenzaCliente->setCodNegozio($registrazione->getCodNegozio());
            $scadenzaCliente->setIdCliente($registrazione->getIdCliente());
            $scadenzaCliente->setNumFattura($registrazione->getNumFattura());
            $scadenzaCliente->setNumFatturaOrig($unaScadenza[ScadenzaCliente::NUM_FATTURA]);
            $scadenzaCliente->setStaScadenza($unaScadenza[ScadenzaCliente::STA_SCADENZA]);

            if ($unaScadenza[ScadenzaCliente::ID_SCADENZA] == 0) {
                $scadenzaCliente->setStaScadenza(self::SCADENZA_APERTA);
                if ($scadenzaCliente->inserisci($db)) {
                    
                } // tutto ok
                else
                    return false;
            }
            else {
                if ($scadenzaCliente->aggiorna($db)) {
                    
                } // tutto ok
                else
                    return false;
            }
        }
        return true;
    }

}
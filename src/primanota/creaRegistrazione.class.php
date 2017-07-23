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

	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::CREA_REGISTRAZIONE])) $_SESSION[self::CREA_REGISTRAZIONE] = serialize(new CreaRegistrazione());
		return unserialize($_SESSION[self::CREA_REGISTRAZIONE]);
	}

	public function start()
	{
		$this->go();
	}

	public function go()
	{
		$registrazione = Registrazione::getInstance();
		$dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
		$utility = Utility::getInstance();

		if ($this->creaRegistrazione($utility, $registrazione, $dettaglioRegistrazione))
			$_SESSION[self::MSG_DA_CREAZIONE] = self::CREA_REGISTRAZIONE_OK;
		else $_SESSION[self::MSG_DA_CREAZIONE] = self::ERRORE_CREAZIONE_REGISTRAZIONE;

		$_SESSION["Obj_primanotacontroller"] = serialize(new PrimanotaController(RicercaRegistrazione::getInstance()));
		$controller = unserialize($_SESSION["Obj_primanotacontroller"]);
		$controller->start();
	}

	public function creaRegistrazione($utility, $registrazione, $dettaglioRegistrazione)
	{
		$scadenzaFornitore = ScadenzaFornitore::getInstance();
		$scadenzaCliente = ScadenzaCliente::getInstance();
		$db = Database::getInstance();
		$db->beginTransaction();

		if ($registrazione->inserisci($db)) {

			foreach ($dettaglioRegistrazione->getDettagliRegistrazione() as $unDettaglio) {
				$this->creaDettaglioRegistrazione($db, $utility, $registrazione, $dettaglioRegistrazione, $unDettaglio, $scadenzaFornitore, $scadenzaCliente);
			}

			/**
			 * Inserisco le eventuali scadenze del fornitore o del cliente
			 */
			if ($registrazione->getIdFornitore() != null) {
				if ($scadenzaFornitore->getDatScadenza() != "") {
					if (!$this->creaScadenzeFornitore($utility, $db, $registrazione, $dettaglioRegistrazione, $scadenzaFornitore)) {
						$db->rollbackTransaction();
						return false;
					}
				}
			}
			else {
				if ($registrazione->getIdCliente() != null) {
					if ($scadenzaCliente->getDatScadenza() != "") {
						if (!$this->creaScadenzeCliente($utility, $db, $registrazione, $dettaglioRegistrazione, $scadenzaCliente)) {
							$db->rollbackTransaction();
							return false;
						}
					}
				}
			}

			/***
			 * Ricalcolo i saldi dei conti
			 */
			$this->ricalcolaSaldi($db, $registrazione->getDatRegistrazione());

			$db->commitTransaction();
			return true;
		}
		else {
			$db->rollbackTransaction();
			return false;
		}
	}

	public function creaDettaglioRegistrazione($db, $utility, $registrazione, $dettaglioRegistrazione, $unDettaglio, $scadenzaFornitore)
	{
		$_cc = explode(" - ", $unDettaglio[DettaglioRegistrazione::COD_CONTO]);	// il codconto del dettaglio contiene anche la descrizione
		$conto = explode(".", $_cc[0]);		// conto e sottoconto separati da un punto

		$dettaglioRegistrazione->setIdRegistrazione($registrazione->getIdRegistrazione());
		$dettaglioRegistrazione->setCodConto($conto[0]);
		$dettaglioRegistrazione->setCodSottoconto($conto[1]);
		$dettaglioRegistrazione->setImpRegistrazione($unDettaglio[DettaglioRegistrazione::IMP_REGISTRAZIONE]);
		$dettaglioRegistrazione->setIndDareavere($unDettaglio[DettaglioRegistrazione::IND_DAREAVERE]);

		/**
		 * Preparo l'importo in scadenza
		 */

		$array = $utility->getConfig();

		if ($registrazione->getIdFornitore() != null) {
			if (strstr($array['contiFornitore'], $conto[0])) {
				$scadenzaFornitore->setImportoScadenza($unDettaglio[DettaglioRegistrazione::IMP_REGISTRAZIONE]);
			}
		}
		elseif ($registrazione->getIdCliente() != null) {
			if (strstr($array['contiCliente'], $conto[0])) {
				$scadenzaCliente->setImportoScadenza($unDettaglio[DettaglioRegistrazione::IMP_REGISTRAZIONE]);
			}
		}

		if (!$dettaglioRegistrazione->inserisci($db)) {
			$db->rollbackTransaction();
			return false;
		}
		return true;
	}

	public function creaScadenzeFornitore($utility, $db, $registrazione, $dettaglioRegistrazione, $scadenzaFornitore)
	{
		$fornitore = Fornitore::getInstance();
		$fornitore->leggi($db);

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

		if ($scadenzaFornitore->getQtaScadenze() > 0)
		{
			$data1 = str_replace("'", "", $registrazione->getDatRegistrazione());					// la datareg arriva con gli apici per il db
			$dataRegistrazione = strtotime(str_replace('/', '-', $data1));

			foreach ($scadenzaFornitore->getScadenze() as $unaScadenza)
			{
				$data = str_replace("'", "", $unaScadenza[ScadenzaFornitore::DAT_SCADENZA]);			// la datascad arriva con gli apici per il db
				$dataScadenza = strtotime(str_replace('/', '-', $data));							// cambio i separatori altrimenti la strtotime non funziona

				if ($dataScadenza > $dataRegistrazione) {
					/**
					 *  se la registrazione è una nota di accredito (causale 1110) inverte il segno dell'importo in modo che venga sottratto al totale
					 *  parziale della data in scadenza
					 */
					$importo_in_scadenza = (strstr($array['notaDiAccredito'], $registrazione->getCodCausale())) ? $unaScadenza[ScadenzaFornitore::IMP_IN_SCADENZA] * (-1) : $unaScadenza[ScadenzaFornitore::IMP_IN_SCADENZA];
					$scadenzaFornitore->setImportoScadenza($importo_in_scadenza);
					$scadenzaFornitore->setDatScadenza($unaScadenza[ScadenzaFornitore::DAT_SCADENZA]);
					if (!$scadenzaFornitore->inserisci($db)) {
						return false;
					}
				}
			}
		}
		return true;
	}

	public function creaScadenzeCliente($db, $registrazione)
	{
		$cliente = Cliente::getInstance();



		if (!$this->creaScadenzaCliente($db, $utility, $cliente, $datascad, $datareg, $importo_in_scadenza, $descreg, $codneg, $numfatt)) {
			$db->rollbackTransaction();
			error_log("Errore inserimento scadenza cliente, eseguito Rollback");
			return FALSE;
		}
	}

	public function ricalcolaSaldi($db, $datRegistrazione)
	{
		$lavoroPianificato = LavoroPianificato::getInstance();
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		if ($array['lavoriPianificatiAttivati'] == "Si") {
			$lavoroPianificato->setDatRegistrazione(str_replace('/', '-', $datRegistrazione));
			$lavoroPianificato->settaDaEseguire($db);
		}
	}

	private function creaScadenzaFornitore($db, $utility, $fornitore, $datascad, $datareg, $causale, $importo_in_scadenza, $descreg, $codneg, $numfatt) {

		if ($_SESSION['datascad'] != "") {

			$data = str_replace("'", "", $datascad);					// la datascad arriva con gli apici per il db
			$dataScadenza = strtotime(str_replace('/', '-', $data));	// cambio i separatori altrimenti la strtotime non funziona

			$data1 = str_replace("'", "", $datareg);					// la datareg arriva con gli apici per il db
			$dataRegistrazione = strtotime(str_replace('/', '-', $data1));

			$tipAddebito_fornitore = "";
			$staScadenza = "00"; 	// aperta

			if ($dataScadenza > $dataRegistrazione) {

				$result_fornitore = $this->leggiIdFornitore($db, $utility, $fornitore);
				foreach(pg_fetch_all($result_fornitore) as $row) {
					$tipAddebito_fornitore = $row['tip_addebito'];
				}

				if ($this->inserisciScadenza($db, $utility, $_SESSION['idRegistrazione'], $datascad, $importo_in_scadenza, $descreg, $tipAddebito_fornitore, $codneg, $fornitore, trim($numfatt), $staScadenza)) {
					return true;
				}
				else return false;
			}
			else return true;
		}
		else {

			if ($_SESSION['scadenzeInserite'] != "") {

				$tipAddebito_fornitore = "";
				$staScadenza = "00"; 	// aperta

				$result_fornitore = $this->leggiIdFornitore($db, $utility, $fornitore);
				foreach(pg_fetch_all($result_fornitore) as $row) {
					$tipAddebito_fornitore = $row['tip_addebito'];
				}

				$d = explode(",", $_SESSION['scadenzeInserite']);
				$progrFattura = 0;

				foreach($d as $ele) {

					$e = explode("#",$ele);
					$datascad = ($e[1] != "") ? "'" . $e[1] . "'" : "null" ;
					$progrFattura += 1;
					$numfatt_generato = "'" . $_SESSION["numfatt"] . "." . $progrFattura . "'";

					/**
					 *  se la registrazione è una nota di accredito (causale 1110) inverte il segno dell'importo in modo che venga sottratto al totale
					 *  parziale della data in scadenza
					 */
					$array = $utility->getConfig();
					$importo_in_scadenza = (strstr($array['notaDiAccredito'], $causale)) ? $e[2] * (-1) : $e[2];

					if (!$this->inserisciScadenza($db, $utility, $_SESSION['idRegistrazione'], $datascad, $importo_in_scadenza,
						$descreg, $tipAddebito_fornitore, $codneg, $fornitore, $numfatt_generato, $staScadenza)) {
						return false;
					}
				}
			}
			return true;
		}
	}

	private function creaScadenzaCliente($db, $utility, $cliente, $datascad, $datareg, $importo_in_scadenza, $descreg, $codneg, $numfatt) {

		$tipAddebito_cliente = "";
		$staScadenza = "00"; 	// aperta

		$result_cliente = $this->leggiIdCliente($db, $utility, $cliente);
		foreach(pg_fetch_all($result_cliente) as $row) {
			$tipAddebito_cliente = $row['tip_addebito'];
		}

		if ($datascad == "null") {
			$datascad = $datareg;
		}

		if ($this->inserisciScadenzaCliente($db, $utility, $_SESSION['idRegistrazione'], $datascad, $importo_in_scadenza, $descreg, $tipAddebito_cliente, $codneg, $cliente, trim($numfatt), $staScadenza)) {
			return true;
		}
		else return false;
	}

	public function preparaPagina($creaRegistrazioneTemplate) {

		require_once 'database.class.php';
		require_once 'utility.class.php';

		$creaRegistrazioneTemplate->setAzione(self::$azioneCreaRegistrazione);
		$creaRegistrazioneTemplate->setConfermaTip("%ml.confermaCreaRegistrazione%");
		$creaRegistrazioneTemplate->setTitoloPagina("%ml.creaNuovaRegistrazione%");

		$db = Database::getInstance();
		$utility = Utility::getInstance();

		// Prelievo dei dati per popolare i combo -------------------------------------------------------------

		$_SESSION['elenco_causali'] = $this->caricaCausali($utility, $db, self::$categoria_causali);
		$_SESSION['elenco_fornitori'] = $this->caricaFornitori($utility, $db);
		$_SESSION['elenco_clienti'] = $this->caricaClienti($utility, $db);

		/**
		 * Prepara la valorizzazione dei conti per la causale. L'ajax di pagina interviene solo sulla selezione
		 * della causale ma se viene fatta la submit del form i conti del dialogo non vengono più valorizzati
		 */
		$_SESSION['elenco_conti'] = $this->caricaConti($utility, $db);
	}
}

?>
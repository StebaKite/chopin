<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'ricercaRegistrazione.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'registrazione.class.php';
require_once 'fornitore.class.php';
require_once 'cliente.class.php';
require_once 'scadenzaFornitore.class.php';
require_once 'scadenzaCliente.class.php';
require_once 'causale.class.php';

class ModificaRegistrazione extends PrimanotaAbstract implements PrimanotaBusinessInterface
{
	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::MODIFICA_REGISTRAZIONE])) $_SESSION[self::MODIFICA_REGISTRAZIONE] = serialize(new ModificaRegistrazione());
		return unserialize($_SESSION[self::MODIFICA_REGISTRAZIONE]);
	}

	public function start()
	{
		$registrazione = Registrazione::getInstance();
		$dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
		$fornitore = Fornitore::getInstance();
		$cliente = Cliente::getInstance();
		$scadenzaFornitore = ScadenzaFornitore::getInstance();
		$scadenzaCliente = ScadenzaCliente::getInstance();
		$causale = Causale::getInstance();

		$utility = Utility::getInstance();
		$db = Database::getInstance();

		$registrazione->leggi($db);
		$_SESSION[self::REGISTRAZIONE] = serialize($registrazione);

		if ($registrazione->getIdFornitore() != null) {
			$fornitore->setIdFornitore($registrazione->getIdFornitore());
			$fornitore->leggi($db);
			$scadenzaFornitore->setIdRegistrazione($registrazione->getIdRegistrazione());
			$scadenzaFornitore->trovaScadenzeRegistrazione($db);
		}

		if ($registrazione->getIdCliente() != null) {
			$cliente->setIdCliente($registrazione->getIdCliente());
			$cliente->leggi($db);
			$scadenzaCliente->setIdRegistrazione($registrazione->getIdRegistrazione());
			$scadenzaCliente->trovaScadenzeRegistrazione($db);
		}

		$dettaglioRegistrazione->setIdRegistrazione($registrazione->getIdRegistrazione());
		$dettaglioRegistrazione->leggiDettagliRegistrazione($db);

		$causale->setCodCausale($registrazione->getCodCausale());
		$causale->loadContiConfigurati($db);

		$datiPagina =
		trim($registrazione->getDatRegistrazione()) . "|" .
		trim($registrazione->getDesRegistrazione()) . "|" .
		trim($registrazione->getCodCausale()) . "|" .
		trim($registrazione->getCodNegozio()) . "|" .
		trim($fornitore->getDesFornitore()) . "|" .
		trim($cliente->getDesCliente()) . "|" .
		trim($registrazione->getNumFattura()) . "|" .
		trim($this->makeTabellaScadenzeFornitore($scadenzaFornitore, "scadenzesuppl_mod")) . "|" .
		trim($this->makeTabellaScadenzeCliente($scadenzaCliente, "scadenzesuppl_mod")) . "|".
		trim($this->makeTabellaDettagliRegistrazione($dettaglioRegistrazione, "dettagli_mod")) . "|" .
		trim($causale->getContiCausale())
		;

		echo $datiPagina;
	}

	public function go()
	{
		$registrazione = Registrazione::getInstance();
		$dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
		$fornitore = Fornitore::getInstance();
		$cliente = Cliente::getInstance();
		$scadenzaFornitore = ScadenzaFornitore::getInstance();
		$scadenzaCliente = ScadenzaCliente::getInstance();
		$causale = Causale::getInstance();
		$utility = Utility::getInstance();

		if ($this->aggiornaRegistrazione($utility, $registrazione, $dettaglioRegistrazione, $scadenzaFornitore, $scadenzaCliente, $fornitore, $cliente))
			$_SESSION[self::MSG_DA_CREAZIONE] = self::CREA_REGISTRAZIONE_OK;
		else $_SESSION[self::MSG_DA_CREAZIONE] = self::ERRORE_CREAZIONE_REGISTRAZIONE;

		$_SESSION["Obj_primanotacontroller"] = serialize(new PrimanotaController(RicercaRegistrazione::getInstance()));
		$controller = unserialize($_SESSION["Obj_primanotacontroller"]);
		$controller->start();
	}

	public function aggiornaRegistrazione($utility, $registrazione, $dettaglioRegistrazione, $scadenzaFornitore, $scadenzaCliente, $fornitore, $cliente)
	{
		$db = Database::getInstance();
		$db->beginTransaction();
		$array = $utility->getConfig();

		if ($registrazione->aggiorna($db))
		{
			if ($this->aggiornaDettagli($db,$utility,$registrazione,$dettaglioRegistrazione))
			{
				/*
				 * Tutto ok, Aggiorno le scadenze fornitore o cliente
				 *
				 * - inserisco quelle aggiunte
				 * - aggiorno quelle esistenti con i dati variati dell'operazione
				 */

				if ($registrazione->getIdFornitore() != null)
				{
					if ($this->aggiornaScadenzeFornitore($db,$utility,$registrazione,$scadenzaFornitore,$fornitore)) {}		// tutto ok
					else {
						$db->rollbackTransaction();
						return false;
					}
				}
				else {
					if ($registrazione->getIdCliente() != null)
					{
						if ($this->aggiornaScadenzeCliente($db,$utility,$registrazione,$scadenzaCliente,$cliente)) {}		// tutto ok
						else {
							$db->rollbackTransaction();
							return false;
						}
					}
				}
				$this->ricalcolaSaldi($db, $registrazione->getDatRegistrazione());
				$db->commitTransaction();
				return true;
			}
			else {
				$db->rollbackTransaction();
				return false;
			}
		}
		else {
			$db->rollbackTransaction();
			return false;
		}
	}

	private function aggiornaDettagli($db,$utility,$registrazione,$dettaglioRegistrazione)
	{
		foreach ($dettaglioRegistrazione->getDettagliRegistrazione() as $unDettaglio)
		{
			if ($unDettaglio[DettaglioRegistrazione::ID_REGISTRAZIONE] == 0)
			{
				$dettaglioRegistrazione->setIdRegistrazione($registrazione->getIdRegistrazione());
				$dettaglioRegistrazione->setImpRegistrazione($unDettaglio[DettaglioRegistrazione::IMP_REGISTRAZIONE]);
				$dettaglioRegistrazione->setIndDareavere($unDettaglio[DettaglioRegistrazione::IND_DAREAVERE]);

				$_cc = explode(" - ", $unDettaglio[DettaglioRegistrazione::COD_CONTO]);
				$conto = explode(".", $_cc[0]);

				$dettaglioRegistrazione->setCodConto($conto[0]);
				$dettaglioRegistrazione->setCodSottoconto($conto[1]);

				if ($dettaglioRegistrazione->inserisci($db)) {}		// tutto ok
				else return false;
			}
		}
		return true;
	}

	private function aggiornaScadenzeFornitore($db,$utility,$registrazione,$scadenzaFornitore,$fornitore)
	{
		foreach ($scadenzaFornitore->getScadenzeDaPagare() as $unaScadenza)
		{
			$scadenzaFornitore->setIdFornitoreOrig($unaScadenza[ScadenzaFornitore::ID_FORNITORE]);
			$scadenzaFornitore->setIdFornitore($registrazione->getIdFornitore());
			$scadenzaFornitore->setIdRegistrazione($registrazione->getIdRegistrazione());
			$scadenzaFornitore->setDatScadenza($unaScadenza[ScadenzaFornitore::DAT_SCADENZA]);

			/**
			 *  se la registrazione è una nota di accredito (causale 1110) inverte il segno dell'importo in scadenza
			 */

			$importo_in_scadenza = (strstr($array['notaDiAccredito'], $registrazione->getCodCausale())) ? $unaScadenza[ScadenzaFornitore::IMP_IN_SCADENZA] * (-1) : $unaScadenza[ScadenzaFornitore::IMP_IN_SCADENZA];

			$scadenzaFornitore->setImpInScadenza($importo_in_scadenza);
			$scadenzaFornitore->setNotaScadenza($registrazione->getDesRegistrazione());
			$scadenzaFornitore->setTipAddebito($fornitore->getTipAddebito());
			$scadenzaFornitore->setCodNegozio($registrazione->getCodNegozio());
			$scadenzaFornitore->setIdFornitore($registrazione->getIdFornitore());
			$scadenzaFornitore->setNumFattura($registrazione->getNumFattura());
			$scadenzaFornitore->setNumFatturaOrig($unaScadenza[ScadenzaFornitore::NUM_FATTURA]);
			$scadenzaFornitore->setStaScadenza($unaScadenza[ScadenzaFornitore::STA_SCADENZA]);

			if ($unaScadenza[ScadenzaFornitore::ID_SCADENZA] == 0)
			{
				$scadenzaFornitore->setStaScadenza(self::SCADENZA_APERTA);
				if ($scadenzaFornitore->inserisci($db)) {}	// tutto ok
				else return false;
			}
			else {
				if ($scadenzaFornitore->aggiorna($db)) {}	// tutto ok
				else return false;
			}
		}
		return true;
	}

	private function aggiornaScadenzeCliente($db,$utility,$registrazione,$scadenzaCliente,$cliente)
	{
		foreach ($scadenzaCliente->getScadenzeDaIncassare() as $unaScadenza)
		{
			$scadenzaCliente->setIdClienteOrig($unaScadenza[ScadenzaCliente::ID_CLIENTE]);
			$scadenzaCliente->setIdCliente($registrazione->getIdCliente());
			$scadenzaCliente->setIdRegistrazione($registrazione->getIdRegistrazione());
			$scadenzaCliente->setDatRegistrazione($unaScadenza[ScadenzaCliente::DAT_REGISTRAZIONE]);

			$scadenzaCliente->setImpRegistrazione($unaScadenza[ScadenzaCliente::IMP_REGISTRAZIONE]);
			$scadenzaCliente->setNota($registrazione->getDesRegistrazione());
			$scadenzaCliente->setTipAddebito($cliente->getTipAddebito());
			$scadenzaCliente->setCodNegozio($registrazione->getCodNegozio());
			$scadenzaCliente->setIdCliente($registrazione->getIdCliente());
			$scadenzaCliente->setNumFattura($registrazione->getNumFattura());
			$scadenzaCliente->setNumFatturaOrig($unaScadenza[ScadenzaCliente::NUM_FATTURA]);
			$scadenzaCliente->setStaScadenza($unaScadenza[ScadenzaCliente::STA_SCADENZA]);

			if ($unaScadenza[ScadenzaCliente::ID_SCADENZA] == 0)
			{
				$scadenzaCliente->setStaScadenza(self::SCADENZA_APERTA);
				if ($scadenzaCliente->inserisci($db)) {}	// tutto ok
				else return false;
			}
			else {
				if ($scadenzaFornitore->aggiorna($db)) {}	// tutto ok
				else return false;
			}
		}
		return true;
	}
}

?>
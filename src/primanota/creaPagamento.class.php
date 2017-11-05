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


class CreaPagamento extends PrimanotaAbstract implements PrimanotaBusinessInterface
{
	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::CREA_PAGAMENTO])) $_SESSION[self::CREA_PAGAMENTO] = serialize(new CreaPagamento());
		return unserialize($_SESSION[self::CREA_PAGAMENTO]);
	}

	public function start()
	{
	    $scadenzaFornitore = ScadenzaFornitore::getInstance();
	    $scadenzaFornitore->setIdTableScadenzeAperte("scadenze_aperte_pag_cre");
	    $scadenzaFornitore->setIdTableScadenzeChiuse("scadenze_chiuse_pag_cre");	
	    $_SESSION[self::SCADENZA_FORNITORE] = serialize($scadenzaFornitore);
	    echo "Ok";
	}

	public function go()
	{
		$registrazione = Registrazione::getInstance();
		$dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
		$utility = Utility::getInstance();

		if ($this->creaPagamento($utility, $registrazione, $dettaglioRegistrazione))
			$_SESSION[self::MSG_DA_CREAZIONE] = self::CREA_PAGAMENTO_OK;
			else $_SESSION[self::MSG_DA_CREAZIONE] = self::ERRORE_CREAZIONE_REGISTRAZIONE;

			$_SESSION["Obj_primanotacontroller"] = serialize(new PrimanotaController(RicercaRegistrazione::getInstance()));
			$controller = unserialize($_SESSION["Obj_primanotacontroller"]);
			$controller->start();
	}

	public function creaPagamento($utility, $registrazione, $dettaglioRegistrazione)
	{
		$scadenzaFornitore = ScadenzaFornitore::getInstance();
		$fornitore = Fornitore::getInstance();
		$db = Database::getInstance();
		$db->beginTransaction();

		$registrazione->setNumFattura("");

		if ($registrazione->inserisci($db))
		{
			foreach ($dettaglioRegistrazione->getDettagliRegistrazione() as $unDettaglio) {
				$this->creaDettaglioPagamento($db, $utility, $registrazione, $dettaglioRegistrazione, $unDettaglio);
			}

			/**
			 * Riconciliazione delle fatture indicate con chiusura delle rispettive scadenze
			 */
			$riconciliazioneFattureOkay = true;

			foreach ($scadenzaFornitore->getScadenzePagate() as $unaScadenza)
			{
				$scadenzaFornitore->setIdFornitore($fornitore->getIdFornitore());
				$scadenzaFornitore->setIdPagamento($registrazione->getIdRegistrazione());
				$scadenzaFornitore->setStaScadenza("10");		// pagata e chiusa

				$scadenzaFornitore->setNumFattura($unaScadenza[ScadenzaFornitore::NUM_FATTURA]);
				$scadenzaFornitore->setDatScadenza($unaScadenza[ScadenzaFornitore::DAT_SCADENZA]);

				if (!$scadenzaFornitore->cambiaStato($db))
				{
					$riconciliazioneFattureOkay = false;
					break;
				}
			}

			/***
			 * Ricalcolo i saldi dei conti
			 */
			if ($riconciliazioneFattureOkay) {
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

	public function creaDettaglioPagamento($db, $utility, $registrazione, $dettaglioRegistrazione, $unDettaglio)
	{
		$_cc = explode(" - ", $unDettaglio[DettaglioRegistrazione::COD_CONTO]);	// il codconto del dettaglio contiene anche la descrizione
		$conto = explode(".", $_cc[0]);		// conto e sottoconto separati da un punto

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

?>
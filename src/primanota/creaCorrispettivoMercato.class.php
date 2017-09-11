<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'creaCorrispettivoMercato.template.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'registrazione.class.php';
require_once 'dettaglioRegistrazione.class.php';
require_once 'ricercaRegistrazione.class.php';
require_once 'lavoroPianificato.class.php';

class CreaCorrispettivoMercato extends primanotaAbstract implements PrimanotaBusinessInterface
{
	function __construct() {

		$this->root = $_SERVER['DOCUMENT_ROOT'];
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::CREA_CORRISPETTIVO_MERCATO])) $_SESSION[self::CREA_CORRISPETTIVO_MERCATO] = serialize(new CreaCorrispettivoMercato());
		return unserialize($_SESSION[self::CREA_CORRISPETTIVO_MERCATO]);
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

		if ($this->creaCorrispettivoMercato($utility, $registrazione, $dettaglioRegistrazione))
			$_SESSION[self::MSG_DA_CREAZIONE] = self::CREA_REGISTRAZIONE_OK;
		else $_SESSION[self::MSG_DA_CREAZIONE] = self::ERRORE_CREAZIONE_REGISTRAZIONE;

		$_SESSION["Obj_primanotacontroller"] = serialize(new PrimanotaController(RicercaRegistrazione::getInstance()));
		$controller = unserialize($_SESSION["Obj_primanotacontroller"]);
		$controller->start();
	}

	public function creaCorrispettivoMercato($utility, $registrazione, $dettaglioRegistrazione)
	{
		$db = Database::getInstance();
		$db->beginTransaction();
		$dettagli_ok = true;

		if ($registrazione->inserisci($db)) {

			foreach ($dettaglioRegistrazione->getDettagliRegistrazione() as $unDettaglio) {
				if ($this->creaDettaglioCorrispettivoMercato($db, $utility, $registrazione, $dettaglioRegistrazione, $unDettaglio)) {}
				else {
					$dettagli_ok = false;
					break;
				}
			}

			/***
			 * Ricalcolo i saldi dei conti
			 */
			if ($dettagli_ok) {
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

	public function creaDettaglioCorrispettivoMercato($db, $utility, $registrazione, $dettaglioRegistrazione, $unDettaglio)
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
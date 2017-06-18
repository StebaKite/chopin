<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.business.interface.php';
require_once 'anagrafica.controller.class.php';
require_once 'ricercaFornitore.class.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'fornitore.class.php';

class ModificaFornitore extends AnagraficaAbstract implements AnagraficaBusinessInterface {

	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::MODIFICA_FORNITORE])) $_SESSION[self::MODIFICA_FORNITORE] = serialize(new ModificaFornitore());
		return unserialize($_SESSION[self::MODIFICA_FORNITORE]);
	}

	public function start()
	{
		$fornitore = Fornitore::getInstance();
		$db = Database::getInstance();
		$utility = Utility::getInstance();

		$fornitore->leggi($db);
		$_SESSION[self::FORNITORE] = serialize($fornitore);

		$datiPagina =
		trim($fornitore->getCodFornitore()) . "|" .
		trim($fornitore->getDesFornitore()) . "|" .
		trim($fornitore->getDesIndirizzoFornitore()) . "|" .
		trim($fornitore->getDesCittaFornitore()) . "|" .
		trim($fornitore->getCapFornitore()) . "|".
		trim($fornitore->getTipAddebito()) . "|" .
		trim($fornitore->getNumGgScadenzaFattura());

		echo $datiPagina;
	}

	public function go()
	{
		$fornitore = Fornitore::getInstance();
		$db = Database::getInstance();
		$utility = Utility::getInstance();

		if ($this->controlliLogici($fornitore)) {

			if ($this->aggiornaFornitore($db, $fornitore)) {
				$_SESSION[self::MSG_DA_MODIFICA] = self::MODIFICA_FORNITORE_OK;
			}
		}
		else {
			$_SESSION[self::MSG_DA_MODIFICA] = $_SESSION[self::MESSAGGIO];
		}

		$_SESSION["Obj_anagraficacontroller"] = serialize(new AnagraficaController(RicercaFornitore::getInstance()));
		$controller = unserialize($_SESSION["Obj_anagraficacontroller"]);
		$controller->start();
	}

	private function aggiornaFornitore($db, $fornitore)
	{
		$db->beginTransaction();

		/**
		 * Metto il doppio apostrofo e gli apici dove servono
		 */

		$fornitore->setDesFornitore(str_replace("'","''",$fornitore->getDesFornitore()));

		$indirizzo = ($fornitore->getDesIndirizzoFornitore() != "") ? "'" . $fornitore->getDesIndirizzoFornitore() . "'" : "null" ;
		$fornitore->setDesIndirizzoFornitore($indirizzo);

		$citta = ($fornitore->getDesCittaFornitore() != "") ? "'" . $fornitore->getDesCittaFornitore() . "'" : "null" ;
		$fornitore->setDesCittaFornitore($citta);

		$cap = ($fornitore->getCapFornitore() != "") ? "'" . $fornitore->getCapFornitore() . "'" : "null" ;
		$fornitore->setCapFornitore($cap);

		if ($fornitore->update($db)) {

			$db->commitTransaction();
			return TRUE;
		}
		else {
			$db->rollbackTransaction();
			error_log("Errore aggiornamento fornitore, eseguito Rollback");
			return FALSE;
		}
	}

	public function controlliLogici($fornitore)
	{
		$esito = TRUE;
		$msg = "<br>";

		/**
		 * Controllo presenza dati obbligatori
		 */

		if ($fornitore->getCodFornitore() == "") {
			$msg = $msg . "<br>&ndash; Manca il codice del fornitore";
			$esito = FALSE;
		}

		if ($fornitore->getDesFornitore() == "") {
			$msg = $msg . "<br>&ndash; Manca la descrizione del fornitore";
			$esito = FALSE;
		}

		// ----------------------------------------------

		if ($msg != "<br>")	$_SESSION["messaggio"] = $msg;
		else unset($_SESSION["messaggio"]);

		return $esito;
	}
}

?>
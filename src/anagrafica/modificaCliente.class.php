<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.business.interface.php';
require_once 'ricercaCliente.class.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'cliente.class.php';
require_once 'categoriaCliente.class.php';

class ModificaCliente extends AnagraficaAbstract implements AnagraficaBusinessInterface {

	function __construct() {

		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::MODIFICA_CLIENTE])) $_SESSION[self::MODIFICA_CLIENTE] = serialize(new ModificaCliente());
		return unserialize($_SESSION[self::MODIFICA_CLIENTE]);
	}

	public function start()
	{
		$cliente = Cliente::getInstance();
		$db = Database::getInstance();
		$utility = Utility::getInstance();

		$cliente->leggi($db);
		$_SESSION[self::CLIENTE] = serialize($cliente);

		$categoriaCliente = CategoriaCliente::getInstance();
		$categoriaCliente->setCatCliente(trim($cliente->getCatCliente()));
		$categoriaCliente->load();
		$_SESSION[self::CATEGORIA_CLIENTE] = serialize($categoriaCliente);

		$selectmenuCategorie =
		"<select class='selectmenuCategoria' id='catcliente_mod' name='catcliente_mod' >" .
		"<option value=''></option>" . $categoriaCliente->getElencoCategorieCliente() .
		"</select>";

		$datiPagina =
		trim($cliente->getCodCliente()) . "|" .
		trim($cliente->getDesCliente()) . "|" .
		trim($cliente->getDesIndirizzoCliente()) . "|" .
		trim($cliente->getDesCittaCliente()) . "|" .
		trim($cliente->getCapCliente()) . "|".
		trim($cliente->getTipAddebito()) . "|" .
		trim($cliente->getCodPiva()) . "|" .
		trim($cliente->getCodFisc()) . "|" .
		trim($selectmenuCategorie);

		echo $datiPagina;
	}

	public function go()
	{
		$cliente = Cliente::getInstance();
		$db = Database::getInstance();
		$utility = Utility::getInstance();

		if ($this->controlliLogici($cliente)) {

			if ($this->aggiornaCliente($db, $cliente)) {
				$_SESSION[self::MSG_DA_MODIFICA] = self::MODIFICA_CLIENTE_OK;
			}
		}
		else {
			$_SESSION[self::MSG_DA_MODIFICA] = $_SESSION[self::MESSAGGIO];
		}

		$_SESSION["Obj_anagraficacontroller"] = serialize(new AnagraficaController(RicercaCliente::getInstance()));
		$controller = unserialize($_SESSION["Obj_anagraficacontroller"]);
		$controller->start();
	}

	private function aggiornaCliente($db, $cliente)
	{
		$db->beginTransaction();

		/**
		 * Metto il doppio apostrofo e gli apici dove servono
		 */

		$cliente->setDesCliente(str_replace("'","''",$cliente->getDesCliente()));

		$indirizzo = ($cliente->getDesIndirizzoCliente() != "") ? "'" . str_replace("'","''",$cliente->getDesIndirizzoCliente()) . "'" : "null" ;
		$cliente->setDesIndirizzoCliente($indirizzo);

		$cittacliente = ($cliente->getDesCittaCliente() != "") ? "'" . str_replace("'","''",$cliente->getDesCittaCliente()) . "'" : "null" ;
		$cliente->setDesCittaCliente($cittacliente);

		$capcliente = ($cliente->getCapCliente() != "") ? "'" . $cliente->getCapCliente() . "'" : "null" ;
		$cliente->setCapCliente($capcliente);

		$codpiva = ($cliente->getCodPiva() != "") ? "'" . $cliente->getCodPiva() . "'" : "null" ;
		$cliente->setCodPiva($codpiva);

		$codfisc = ($cliente->getCodFisc() != "") ? "'" . $cliente->getCodFisc() . "'" : "null" ;
		$cliente->setCodFisc($codfisc);

		$catcliente = ($cliente->getCatCliente() != "") ? "'" . $cliente->getCatCliente() . "'" : "null" ;
		$cliente->setCatCliente($catcliente);

		if ($cliente->update($db)) {

			$db->commitTransaction();
			return TRUE;
		}
		else {
			$db->rollbackTransaction();
			error_log("Errore aggiornamento cliente, eseguito Rollback");
			return FALSE;
		}
	}

	public function controlliLogici($cliente)
	{
		$esito = TRUE;
		$msg = "<br>";

		/**
		 * Controllo presenza dati obbligatori
		 */

		if ($cliente->getCatCliente() == "") {
			$msg = $msg . "<br>&ndash; Manca la categoria del cliente";
			$esito = FALSE;
		}

		if ($cliente->getCodCliente() == "") {
			$msg = $msg . "<br>&ndash; Manca il codice del cliente";
			$esito = FALSE;
		}

		if ($cliente->getDesCliente() == "") {
			$msg = $msg . "<br>&ndash; Manca la descrizione del cliente";
			$esito = FALSE;
		}

		if (($cliente->getEsitoPivaCliente() != "P.iva Ok!") and ($cliente->getEsitoPivaCliente() != "")) {
			$msg = $msg . "<br>&ndash; P.iva cliente gi&agrave; esistente";
			unset($_SESSION["codpiva"]);
			$esito = FALSE;
		}

		if (($cliente->getEsitoCfisCliente() != "C.fisc Ok!") and ($cliente->getEsitoCfisCliente() != "")) {
			$msg = $msg . "<br>&ndash; C.fisc cliente gi&agrave; esistente";
			unset($_SESSION["codfisc"]);
			$esito = FALSE;
		}

		// ----------------------------------------------

		if ($msg != "<br>") {
			$_SESSION["messaggio"] = $msg;
		}
		else {
			unset($_SESSION["messaggio"]);
		}

		return $esito;
	}
}

?>
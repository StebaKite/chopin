<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.business.interface.php';
require_once 'anagrafica.controller.class.php';
require_once 'ricercaCliente.class.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'cliente.class.php';
require_once 'categoriaCliente.class.php';

class CreaCliente extends AnagraficaAbstract implements AnagraficaBusinessInterface
{
	function __construct() {

		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();

		$this->testata = $this->root . $this->array[self::TESTATA];
		$this->piede = $this->root . $this->array[self::PIEDE];
		$this->messaggioErrore = $this->root . $this->array[self::ERRORE];
		$this->messaggioInfo = $this->root . $this->array[self::INFO];
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::CREA_CLIENTE])) $_SESSION[self::CREA_CLIENTE] = serialize(new CreaCliente());
		return unserialize($_SESSION[self::CREA_CLIENTE]);
	}

	public function start()
	{
		$this->go();
	}

	public function go()
	{

		if ($this->controlliLogici()) {

			if ($this->creaCliente()) {
				$_SESSION[self::MSG_DA_CREAZIONE] = self::CREA_CLIENTE_OK;
			}
			else $_SESSION[self::MSG_DA_CREAZIONE] = self::ERRORE_CREA_CLIENTE;
		}
		else $_SESSION[self::MSG_DA_CREAZIONE] = $_SESSION[self::MESSAGGIO];

		$_SESSION["Obj_anagraficacontroller"] = serialize(new AnagraficaController(RicercaCliente::getInstance()));
		$controller = unserialize($_SESSION["Obj_anagraficacontroller"]);
		$controller->start();
	}

	private function creaCliente($utility)
	{
		$cliente = Cliente::getInstance();

		$descrizione = ($cliente->getDesCliente() != "") ? str_replace("'","''",$cliente->getDesCliente()) : "" ;
		$cliente->setDesCliente($descrizione);

		$indirizzo = ($cliente->getDesIndirizzoCliente() != "") ? "'" . str_replace("'","''",$cliente->getDesIndirizzoCliente()) . "'" : "null" ;
		$cliente->setDesIndirizzoCliente($indirizzo);

		$citta = ($cliente->getDesCittaCliente() != "") ? "'" . str_replace("'","''",$cliente->getDesCittaCliente()) . "'" : "null" ;
		$cliente->setDesCittaCliente($citta);

		$cap = ($cliente->getCapCliente() != "") ? "'" . $cliente->getCapCliente() . "'" : "null";
		$cliente->setCapCliente($cap);

		$piva = ($cliente->getCodPiva() != "") ? "'" . $cliente->getCodPiva() . "'" : "null" ;
		$cliente->setCodPiva($piva);

		$cfis = ($cliente->getCodFisc() != "") ? "'" . $cliente->getCodFisc() . "'" : "null" ;
		$cliente->setCodFisc($cfis);

		$cat = ($cliente->getCatCliente() != "") ? "'" . $cliente->getCatCliente() . "'" : "null" ;
		$cliente->setCatCliente($cat);

		$_SESSION[self::CLIENTE] = serialize($cliente);

		$db = Database::getInstance();
		$db->beginTransaction();

		if ($cliente->inserisci($db)) {

			$db->commitTransaction();
			return TRUE;
		}
		$db->rollbackTransaction();
		$_SESSION[self::MESSAGGIO] = self::ERRORE_CREA_CLIENTE;
		return FALSE;
	}

	public function controlliLogici()
	{
		$cliente = Cliente::getInstance();

		$esito = TRUE;
		$msg = "<br>";

		/**
		 * Controllo presenza dati obbligatori
		 */

		if ($cliente->getCatCliente() == "") {
			$msg = $msg . self::ERRORE_CATEGORIA_CLIENTE;
			$esito = FALSE;
		}

		if ($cliente->getCodCliente() == "") {
			$msg = $msg . self::ERRORE_CODICE_CLIENTE;
			$esito = FALSE;
		}

		if ($cliente->getDesCliente() == "") {
			$msg = $msg . self::ERRORE_DESCRIZIONE_CLIENTE;
			$esito = FALSE;
		}

		// 		if ($_SESSION["codfisc"] != "") {

		// 			include_once 'cf.class.php';

		// 			$cf = new CodiceFiscale();
		// 			$cf->SetCF($_SESSION["codfisc"]);
		// 			if (!($cf->GetCodiceValido())) {
		// 				$msg = $msg . "<br>&ndash; Codice fiscale non corretto";
		// 				$esito = FALSE;
		// 			}
		// 		}

		if (($cliente->getEsitoPivaCliente() != "P.iva Ok!") and ($cliente->getEsitoPivaCliente() != "")) {
			$msg = $msg . self::ERRORE_PIVA_CLIENTE;
			$cliente->setCodPiva(null);
			$esito = FALSE;
		}

		if (($cliente->getEsitoCfisCliente() != "C.fisc Ok!") and ($cliente->getEsitoCfisCliente() != "")) {
			$msg = $msg . self::ERRORE_CFISC_CLIENTE;
			$cliente->setCodFisc(null);
			$esito = FALSE;
		}

		// ----------------------------------------------

		if ($msg != "<br>") {
			$_SESSION[self::MESSAGGIO] = $msg;
		}
		else {
			unset($_SESSION[self::MESSAGGIO]);
		}
		return $esito;
	}
}

?>

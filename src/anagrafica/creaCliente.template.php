<?php

require_once "anagrafica.abstract.class.php";
require_once "anagrafica.presentation.interface.php";
require_once "cliente.class.php";
require_once "categoriaCliente.class.php";
require_once 'utility.class.php';

class CreaClienteTemplate extends AnagraficaAbstract implements AnagraficaPresentationInterface
{
	function __construct()
	{
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
		if (!isset($_SESSION[self::CREA_CLIENTE_TEMPLATE])) $_SESSION[self::CREA_CLIENTE_TEMPLATE] = serialize(new CreaClienteTemplate());
		return unserialize($_SESSION[self::CREA_CLIENTE_TEMPLATE]);
	}

	// template ------------------------------------------------

	public function inizializzaPagina() {}

	public function controlliLogici() {

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

	public function displayPagina() {

		$cliente = Cliente::getInstance();
		$categoriaCliente = CategoriaCliente::getInstance();
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$form = $this->root . $array['template'] . self::PAGINA_CREA_CLIENTE;

		$replace = array(
				'%titoloPagina%' => $this->getTitoloPagina(),
				'%elenco_categorie_cliente%' => $categoriaCliente->getElencoCategorieCliente(),
				'%azione%' => $this->getAzione(),
				'%confermaTip%' => $this->getConfermaTip(),
				'%codcliente%' => $cliente->getCodCliente(),
				'%descliente%' => $cliente->getDesCliente(),
				'%indcliente%' => $cliente->getDesIndirizzoCliente(),
				'%cittacliente%' => $cliente->getDesCittaCliente(),
				'%capcliente%' => $cliente->getCapCliente(),
				'%tipoaddebito%' => $cliente->getTipAddebito(),
				'%codpiva%' => $cliente->getCodPiva(),
				'%codfisc%' => $cliente->getCodFisc(),
				'%catcliente%' => $cliente->getCatCliente()
		);

		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}

?>

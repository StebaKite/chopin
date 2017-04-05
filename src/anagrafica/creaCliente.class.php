<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.business.interface.php';
require_once 'anagrafica.controller.class.php';
require_once 'creaCliente.template.php';
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
		$creaClienteTemplate = CreaClienteTemplate::getInstance();
		$this->preparaPagina($creaClienteTemplate);

		$cliente = Cliente::getInstance();
		$categoriaCliente = CategoriaCliente::getInstance();
		$cliente->prepara();
		$categoriaCliente->load();

		$_SESSION[self::CLIENTE] = serialize($cliente);
		$_SESSION[self::CATEGORIA_CLIENTE] = serialize($categoriaCliente);

		// Compone la pagina
		$replace = (isset($_SESSION[self::AMBIENTE]) ? array('%amb%' => $_SESSION[self::AMBIENTE], '%menu%' => $this->makeMenu($this->utility)) : array('%amb%' => $this->getEnvironment ( $this->array, $_SESSION ), '%menu%' => $this->makeMenu($this->utility)));
		$template = $this->utility->tailFile($this->utility->getTemplate($this->testata), $replace);
		echo $this->utility->tailTemplate($template);

		$creaClienteTemplate->displayPagina();
		include($this->piede);
	}

	public function go()
	{
		$creaClienteTemplate = CreaClienteTemplate::getInstance();

		if ($creaClienteTemplate->controlliLogici()) {

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

	private function preparaPagina($creaClienteTemplate)
	{
		$creaClienteTemplate->setAzione(self::AZIONE_CREA_CLIENTE);
		$creaClienteTemplate->setConfermaTip("%ml.confermaCreaCliente%");
		$creaClienteTemplate->setTitoloPagina("%ml.creaNuovoCliente%");
	}
}

?>

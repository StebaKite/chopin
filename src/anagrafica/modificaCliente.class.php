<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.business.interface.php';
require_once 'modificaCliente.template.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'cliente.class.php';
require_once 'categoriaCliente.class.php';

class ModificaCliente extends AnagraficaAbstract implements AnagraficaBusinessInterface {

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
		if (!isset($_SESSION[self::MODIFICA_CLIENTE])) $_SESSION[self::MODIFICA_CLIENTE] = serialize(new ModificaCliente());
		return unserialize($_SESSION[self::MODIFICA_CLIENTE]);
	}

	public function start() {

		$modificaClienteTemplate = ModificaClienteTemplate::getInstance();
		$this->preparaPagina($modificaClienteTemplate);
		
		$db = Database::getInstance();		
		$cliente = Cliente::getInstance();
		$cliente->leggi($db);
		$_SESSION[self::CLIENTE] = serialize($cliente);
		
		$categoriaCliente = CategoriaCliente::getInstance();
		$categoriaCliente->load();
		$_SESSION[self::CATEGORIA_CLIENTE] = serialize($categoriaCliente);
		
		// Compone la pagina
		$replace = (isset($_SESSION[self::AMBIENTE]) ? array('%amb%' => $_SESSION[self::AMBIENTE], '%menu%' => $this->makeMenu($this->utility)) : array('%amb%' => $this->getEnvironment ( $this->array, $_SESSION ), '%menu%' => $this->makeMenu($this->utility)));
		$template = $this->utility->tailFile($this->utility->getTemplate($this->testata), $replace);
		echo $this->utility->tailTemplate($template);
		
		$modificaClienteTemplate->displayPagina();
		include($this->piede);
	}

	public function go() {
		
		$modificaClienteTemplate = ModificaClienteTemplate::getInstance();

		if ($modificaClienteTemplate->controlliLogici()) {

			// Aggiornamento del DB ------------------------------

			if ($this->aggiornaCliente()) {

				$_SESSION["messaggio"] = "Cliente salvato con successo";

				$this->preparaPagina($modificaClienteTemplate);

				$replace = (isset($_SESSION[self::AMBIENTE]) ? array('%amb%' => $_SESSION[self::AMBIENTE], '%menu%' => $this->makeMenu($this->utility)) : array('%amb%' => $this->getEnvironment ( $this->array, $_SESSION ), '%menu%' => $this->makeMenu($this->utility)));
				$template = $this->utility->tailFile($this->utility->getTemplate($this->testata), $replace);
				echo $this->utility->tailTemplate($template);
				
				$modificaClienteTemplate->displayPagina();

				self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
				$template = $this->utility->tailFile($this->utility->getTemplate($this->messaggioInfo), self::$replace);
				echo $this->utility->tailTemplate($template);
					
				include($this->piede);
			}
			else {
				
				$_SESSION["messaggio"] = "Errore durante l'aggiornamento del cliente";
				
				$this->preparaPagina($modificaClienteTemplate);

				$replace = (isset($_SESSION[self::AMBIENTE]) ? array('%amb%' => $_SESSION[self::AMBIENTE], '%menu%' => $this->makeMenu($this->utility)) : array('%amb%' => $this->getEnvironment ( $this->array, $_SESSION ), '%menu%' => $this->makeMenu($this->utility)));
				$template = $this->utility->tailFile($this->utility->getTemplate($this->testata), $replace);
				echo $this->utility->tailTemplate($template);
				
				$modificaClienteTemplate->displayPagina();
					
				self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
				$template = $this->utility->tailFile($this->utility->getTemplate($this->messaggioErrore), self::$replace);
				echo $this->utility->tailTemplate($template);
				
				include($this->piede);
			}
		}
		else {

			$this->preparaPagina($modificaClienteTemplate);

			$replace = (isset($_SESSION[self::AMBIENTE]) ? array('%amb%' => $_SESSION[self::AMBIENTE], '%menu%' => $this->makeMenu($this->utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($this->utility)));
			$template = $this->utility->tailFile($this->utility->getTemplate($this->testata), $replace);
			echo $this->utility->tailTemplate($template);
				
			$modificaClienteTemplate->displayPagina();
				
			self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
			$template = $this->utility->tailFile($this->utility->getTemplate($this->messaggioErrore), self::$replace);
			echo $this->utility->tailTemplate($template);
				
			include($this->piede);				
		}
	}

	private function aggiornaCliente() {

		$cliente = Cliente::getInstance();
		
		$db = Database::getInstance();
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

	private function preparaPagina($modificaClienteTemplate) {

		$modificaClienteTemplate->setAzione(self::AZIONE_MODIFICA_CLIENTE);
		$modificaClienteTemplate->setConfermaTip("%ml.salvaTip%");
		$modificaClienteTemplate->setTitoloPagina("%ml.modificaCliente%");
	}
}

?>
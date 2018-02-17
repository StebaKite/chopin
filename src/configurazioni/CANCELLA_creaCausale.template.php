<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.presentation.interface.php';
require_once 'utility.class.php';
require_once 'causale.class.php';


class CreaCausaleTemplate extends ConfigurazioniAbstract implements ConfigurazioniPresentationInterface
{

	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::CREA_CAUSALE_TEMPLATE])) $_SESSION[self::CREA_CAUSALE_TEMPLATE] = serialize(new CreaCausaleTemplate());
		return unserialize($_SESSION[self::CREA_CAUSALE_TEMPLATE]);
	}

	public function inizializzaPagina() {}

	public function controlliLogici() {

		$causale = Causale::getInstance();
		$esito = TRUE;
		$msg = "<br>";
		
		/**
		 * Controllo presenza dati obbligatori
		 */
		
		if ($causale->getCodCausale() == "") {
			$msg = $msg . self::ERRORE_CODICE_CAUSALE;
			$esito = FALSE;
		}
		else {
			if (!is_numeric($causale->getCodCausale())) {
				$msg = $msg . self::ERRORE_CODICE_CAUSALE_NUMERICO;
				$esito = FALSE;
			}
			else {
				if ($causale->getCodCausale() < 1000) {
					$msg = $msg . self::ERRORE_CODICE_CAUSALE_INVALIDO;
					$esito = FALSE;
				}
			}
		}
		
		if ($causale->getDesCausale() == "") {
			$msg = $msg . self::ERRORE_DESCRIZIONE_CAUSALE;
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
	
		$causale = Causale::getInstance();
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
	
		$form = $this->root . $array['template'] . self::PAGINA_CREA_CAUSALE;
						
		$replace = array(
				'%titoloPagina%' => $this->getTitoloPagina(),
				'%azione%' => $this->getAzione(),
				'%confermaTip%' => $this->getConfermaTip(),
				'%codcausale%' => $causale->getCodCausale(),
				'%descausale%' => $causale->getDesCausale(),
				'%catcausale%' => $causale->getCatCausale()
		);
	
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}

?>
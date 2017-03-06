<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.business.interface.php';
require_once 'modificaFornitore.template.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'fornitore.class.php';

class ModificaFornitore extends AnagraficaAbstract implements AnagraficaBusinessInterface {

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
		if (!isset($_SESSION[self::MODIFICA_FORNITORE])) $_SESSION[self::MODIFICA_FORNITORE] = serialize(new ModificaFornitore());
		return unserialize($_SESSION[self::MODIFICA_FORNITORE]);
	}

	public function start() {

		$modificaFornitoreTemplate = ModificaFornitoreTemplate::getInstance();
		$this->preparaPagina($modificaFornitoreTemplate);
		
		$db = Database::getInstance();
		$fornitore = Fornitore::getInstance();
		$fornitore->leggi($db);
		$_SESSION[self::FORNITORE] = serialize($fornitore);
				
		// Compone la pagina
		$replace = (isset($_SESSION[self::AMBIENTE]) ? array('%amb%' => $_SESSION[self::AMBIENTE], '%menu%' => $this->makeMenu($this->utility)) : array('%amb%' => $this->getEnvironment ( $this->array, $_SESSION ), '%menu%' => $this->makeMenu($this->utility)));
		$template = $this->utility->tailFile($this->utility->getTemplate($this->testata), $replace);
		echo $this->utility->tailTemplate($template);
		
		$modificaFornitoreTemplate->displayPagina();
		include($this->piede);
	}
	
	public function go() {

		$modificaFornitoreTemplate = ModificaFornitoreTemplate::getInstance();
		
		if ($modificaFornitoreTemplate->controlliLogici()) {
		
			// Aggiornamento del DB ------------------------------
		
			if ($this->aggiornaFornitore()) {
		
				$_SESSION["messaggio"] = "Fornitore salvato con successo";
		
				$this->preparaPagina($modificaFornitoreTemplate);

				$replace = (isset($_SESSION[self::AMBIENTE]) ? array('%amb%' => $_SESSION[self::AMBIENTE], '%menu%' => $this->makeMenu($this->utility)) : array('%amb%' => $this->getEnvironment ( $this->array, $_SESSION ), '%menu%' => $this->makeMenu($this->utility)));
				$template = $this->utility->tailFile($this->utility->getTemplate($this->testata), $replace);
				echo $this->utility->tailTemplate($template);
				
				$modificaFornitoreTemplate->displayPagina();

				self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
				$template = $this->utility->tailFile($this->utility->getTemplate($this->messaggioInfo), self::$replace);
				echo $this->utility->tailTemplate($template);
									
				include($this->piede);
			}
			else {
					
				$this->preparaPagina($modificaFornitoreTemplate);

				$replace = (isset($_SESSION[self::AMBIENTE]) ? array('%amb%' => $_SESSION[self::AMBIENTE], '%menu%' => $this->makeMenu($this->utility)) : array('%amb%' => $this->getEnvironment ( $this->array, $_SESSION ), '%menu%' => $this->makeMenu($this->utility)));
				$template = $this->utility->tailFile($this->utility->getTemplate($this->testata), $replace);
				echo $this->utility->tailTemplate($template);
				
				$modificaFornitoreTemplate->displayPagina();

				self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
				$template = $this->utility->tailFile($this->utility->getTemplate($this->messaggioErrore), self::$replace);
				echo $this->utility->tailTemplate($template);
					
				include($this->piede);
			}
		}
	}

	private function aggiornaFornitore($utility) {

		$fornitore = Fornitore::getInstance();
		
		$db = Database::getInstance();
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
	
	private function preparaPagina($modificaFornitoreTemplate) {
	
		$modificaFornitoreTemplate->setAzione(self::AZIONE_MODIFICA_FORNITORE);
		$modificaFornitoreTemplate->setConfermaTip("%ml.salvaTip%");
		$modificaFornitoreTemplate->setTitoloPagina("%ml.modificaFornitore%");
	}
}
		
?>
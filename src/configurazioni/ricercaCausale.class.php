<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'ricercaCausale.template.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'causale.class.php';

class RicercaCausale extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface
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
		if (!isset($_SESSION[self::RICERCA_CAUSALE])) $_SESSION[self::RICERCA_CAUSALE] = serialize(new RicercaCausale());
		return unserialize($_SESSION[self::RICERCA_CAUSALE]);
	}

	public function start()
	{
		$this->go();
	}

	public function go()
	{		
		$causale = Causale::getInstance();
		$db = Database::getInstance();
		$utility = Utility::getInstance();
		$array = $utility->getConfig();		
		$ricercaCausaleTemplate = RicercaCausaleTemplate::getInstance();

		$this->preparaPagina($ricercaCausaleTemplate);
		
		$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
		$template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
		echo $utility->tailTemplate($template);
		
		if ($this->refreshCausali($db, $causale)) {
			
			$ricercaCausaleTemplate->displayPagina();
			
			/**
			 * Gestione del messaggio proveniente dalla cancellazione
			 */
			
			if (isset($_SESSION[self::MSG_DA_CANCELLAZIONE])) {
				$_SESSION[self::MESSAGGIO] = $_SESSION[self::MSG_DA_CANCELLAZIONE] . "<br>" . "Trovate " . $causale->getQtaCausali() . " causali";
				unset($_SESSION[self::MSG_DA_CANCELLAZIONE]);
			}
			elseif (isset($_SESSION[self::MSG_DA_CREAZIONE])) {
				$_SESSION[self::MESSAGGIO] = $_SESSION[self::MSG_DA_CREAZIONE] . "<br>" . "Trovate " . $causale->getQtaCausali() . " causali";
				unset($_SESSION[self::MSG_DA_CREAZIONE]);
			}
			else {
				$_SESSION[self::MESSAGGIO] = "Trovate " . $causale->getQtaCausali() . " causali";
			}	
			
			self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
				
			if ($causale->getQtaCausali() > 0) {
				$template = $utility->tailFile($utility->getTemplate($this->messaggioInfo), self::$replace);
			}
			else {
				$template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
			}
			
			echo $utility->tailTemplate($template);
		}			
		else {
			
			self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
			$template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);			
		}
	
		include($this->piede);
	}

	/**
	 * Questo metodo osserva il contenuto dell'array causali dell'oggetto. Se è vuoto lo ricarica e 
	 * ri-serializza l'oggetto in sessione, se è pieno non fa nulla e lascia l'array esistente
	 * @param unknown $db
	 * @param unknown $causale
	 * @return boolean
	 */
	private function refreshCausali($db, $causale) {

		if (sizeof($causale->getCausali()) == 0) {
				
			if (!$causale->load($db)) {		
				$_SESSION[self::MESSAGGIO] = self::ERRORE_LETTURA ;					
				return false;
			}
			$_SESSION[self::CAUSALE] = serialize($causale);
		}
		return true;
	}
	
	public function preparaPagina($ricercaCausaleTemplate) {
	
		$_SESSION[self::AZIONE] = self::AZIONE_RICERCA_CAUSALE;
		$_SESSION[self::TIP_CONFERMA] = "%ml.cercaTip%";
		$_SESSION[self::TITOLO_PAGINA] = "%ml.ricercaCausale%";
	}
}

?>
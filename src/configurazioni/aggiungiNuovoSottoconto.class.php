<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'utility.class.php';
require_once 'sottoconto.class.php';

class AggiungiNuovoSottoconto extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface  {

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
		if (!isset($_SESSION[self::AGGIUNGI_SOTTOCONTO])) $_SESSION[self::AGGIUNGI_SOTTOCONTO] = serialize(new AggiungiNuovoSottoconto());
		return unserialize($_SESSION[self::AGGIUNGI_SOTTOCONTO]);
	}





?>
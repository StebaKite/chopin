<?php

require_once 'configurazioni.abstract.class.php';
require_once "configurazioni.presentation.interface.php";
require_once "progressivoFattura.class.php";
require_once 'utility.class.php';

class ModificaProgressivoFatturaTemplate extends ConfigurazioniAbstract implements ConfigurazioniPresentationInterface
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
		if (!isset($_SESSION[self::AGGIORNA_PROGRESSIVO_FATTURA_TEMPLATE])) $_SESSION[self::AGGIORNA_PROGRESSIVO_FATTURA_TEMPLATE] = serialize(new ModificaProgressivoFatturaTemplate());
		return unserialize($_SESSION[self::AGGIORNA_PROGRESSIVO_FATTURA_TEMPLATE]);
	}

	public function inizializzaPagina() {}

	public function controlliLogici() {
	
		$esito = TRUE;
		$msg = "<br>";
	
		if ($msg != "<br>") {
			$_SESSION["messaggio"] = $msg;
		}
		else {
			unset($_SESSION["messaggio"]);
		}
	
		return $esito;
	}

	public function displayPagina() {
	
		$progressivoFattura = ProgressivoFattura::getInstance();
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
	
		$form = $this->root . $array['template'] . self::PAGINA_AGGIORNA_PROGRESSIVO_FATTURA;
	
		$replace = array(
				'%titoloPagina%' => $this->getTitoloPagina(),
				'%azione%' => $this->getAzione(),
				'%confermaTip%' => $this->getConfermaTip(),
				'%catcliente%' => $progressivoFattura->getCatCliente(),
				'%codneg%' => $progressivoFattura->getNegProgr(),
				'%numfatt%' => $progressivoFattura->getNumFatturaUltimo(),
				'%notatesta%' => str_replace("'", "&apos;", $progressivoFattura->getNotaTestaFattura()),				
				'%notapiede%' => str_replace("'", "&apos;", $progressivoFattura->getNotaPiedeFattura())
		);
	
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}
	
?>
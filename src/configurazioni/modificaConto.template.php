<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.presentation.interface.php';
require_once 'conto.class.php';
require_once 'sottoconto.class.php';
require_once 'utility.class.php';

class ModificaContoTemplate extends ConfigurazioniAbstract implements ConfigurazioniPresentationInterface
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
		if (!isset($_SESSION[self::MODIFICA_CONTO_TEMPLATE])) $_SESSION[self::MODIFICA_CONTO_TEMPLATE] = serialize(new ModificaContoTemplate());
		return unserialize($_SESSION[self::MODIFICA_CONTO_TEMPLATE]);
	}

	public function inizializzaPagina() {}

	public function controlliLogici() {
		
		$conto = Conto::getInstance();
		$sottoconto = Sottoconto::getInstance();
		
		$esito = TRUE;
		$msg = "<br>";
	
		/**
		 * Controllo presenza dati obbligatori
		 */
	
		if ($conto->getDesConto() == "") {
			$msg = $msg . self::ERRORE_DESCRIZIONE_CONTO;
			$esito = FALSE;
		}
		
		if ($sottoconto->getSottocontiInseriti() == "") {
			$msg = $msg . self::ERRORE_ASSENZA_SOTTOCONTI;
			$esito = FALSE;
		}
		
		if ($msg != "<br>") {
			$_SESSION[self::MESSAGGIO] = $msg;
		}
		else {
			unset($_SESSION[self::MESSAGGIO]);
		}	
		return $esito;
	}

	public function displayPagina() {

		$conto = Conto::getInstance();
		$sottoconto = Sottoconto::getInstance();
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
	
		$form = $this->root . $array['template'] . self::PAGINA_MODIFICA_CONTO;
	
		$tbodySottoconti = "";
	
		foreach ($sottoconto->getSottoconti() as $row) {
	
			$bottoneCancella = "<td width='28' align='right'>" . $row[self::NUM_REG_SOTTOCONTO] . "</td>";
			
			if ($row[self::NUM_REG_SOTTOCONTO] == 0) {
				$bottoneCancella = self::CANCELLA_SOTTOCONTO_HREF . $row[Sottoconto::COD_SOTTOCONTO] . "," . $conto->getCodConto() . self::CANCELLA_SOTTOCONTO_ICON ;
			}

			if ($row[Sottoconto::IND_GRUPPO] == "") $indGruppo = "&ndash;&ndash;&ndash;";			
			elseif ($row[Sottoconto::IND_GRUPPO] == self::NESSUNO) $indGruppo = "&ndash;&ndash;&ndash;";
			elseif ($row[Sottoconto::IND_GRUPPO] == self::COSTI_FISSI) $indGruppo = "Costi Fissi";
			elseif ($row[Sottoconto::IND_GRUPPO] == self::COSTI_VARIABILI) $indGruppo = "Costi Variabili";
			elseif ($row[Sottoconto::IND_GRUPPO] == self::RICAVI) $indGruppo = "Ricavi";

			
			$tbodySottoconti = $tbodySottoconti .
			"<tr>" .
			"	<td>" . $row[Sottoconto::COD_SOTTOCONTO] . "</td>" .
			"	<td>" . $row[Sottoconto::DES_SOTTOCONTO] . "</td>" .			
			"	<td>" . $indGruppo . "</td>" .			
			self::MODIFICA_GRUPPO_SOTTOCONTO_HREF . '"' . $row[Sottoconto::IND_GRUPPO] . '","' . $row[Sottoconto::COD_SOTTOCONTO] . '"' . self::MODIFICA_GRUPPO_SOTTOCONTO_ICON .
			$bottoneCancella .
			"</tr>";
		}
		
		$replace = array(
				'%titoloPagina%' => $this->getTitoloPagina(),
				'%azione%' => $this->getAzione(),
				'%confermaTip%' => $this->getConfermaTip(),
				'%codconto%' => $conto->getCodConto(),
				'%desconto%' => str_replace("'", "&apos;", $conto->getDesConto()),
				'%contoeco_checked%' => (trim($conto->getCatConto()) == self::CONTO_ECONOMICO) ? "checked" : "",
				'%contopat_checked%' => (trim($conto->getCatConto()) == self::STATO_PATRIMONIALE) ? "checked" : "",
				'%dare_checked%' => (trim($conto->getTipConto()) == self::DARE) ? "checked" : "",
				'%avere_checked%' => (trim($conto->getTipConto()) == self::AVERE) ? "checked" : "",
				'%presenzaSi_checked%' => (trim($conto->getIndPresenzaInBilancio()) == "S") ? "checked" : "",
				'%presenzaNo_checked%' => (trim($conto->getIndPresenzaInBilancio()) == "N") ? "checked" : "",
				'%sottocontiSi_checked%' => (trim($conto->getIndVisibilitaSottoconti()) == "S") ? "checked" : "",
				'%sottocontiNo_checked%' => (trim($conto->getIndVisibilitaSottoconti()) == "N") ? "checked" : "",
				'%numrigabilancio%' => ($conto->getNumRigaBilancio() != "") ? $conto->getNumRigaBilancio() : 0,
				'%categoria%' => $conto->getCatConto(),
				'%tipoconto%' => $conto->getTipConto(),
				'%tbody_sottoconti%' => $tbodySottoconti
		);
	
		$utility = Utility::getInstance();
	
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}

?>
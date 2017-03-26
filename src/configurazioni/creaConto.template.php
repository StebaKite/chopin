<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.presentation.interface.php';
require_once 'utility.class.php';
require_once 'conto.class.php';
require_once 'sottoconto.class.php';

class CreaContoTemplate extends ConfigurazioniAbstract implements ConfigurazioniPresentationInterface
{
	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::CREA_CONTO_TEMPLATE])) $_SESSION[self::CREA_CONTO_TEMPLATE] = serialize(new CreaContoTemplate());
		return unserialize($_SESSION[self::CREA_CONTO_TEMPLATE]);
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
		
		if ($conto->getCodConto() == "") {			
			$msg = $msg . self::ERRORE_CODICE_CONTO;
			$esito = FALSE;
		}
		else {
			if (!is_numeric($conto->getCodConto())) {
				$msg = $msg . self::ERRORE_CODICE_CONTO_NUMERICO;
				$esito = FALSE;
			}
			else {
				if ($conto->getCodConto() < 100) {
					$msg = $msg . self::ERRORE_CODICE_CONTO_INVALIDO;
					$esito = FALSE;						
				}
			}
		}
		
		if ($conto->getDesConto() == "") {
			$msg = $msg . self::ERRORE_DESCRIZIONE_CONTO;
			$esito = FALSE;
		}

		if (sizeof($sottoconto->getNuoviSottoconti()) == 0) {
			$msg = $msg . self::ERRORE_ASSENZA_SOTTOCONTI;
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
	
		$conto = Conto::getInstance();
		$sottoconto = Sottoconto::getInstance();
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$form = $this->root . $array['template'] . self::PAGINA_CREA_CONTO;
	
		/**
		 * Prepara la tabella dei dettagli inseriti
		 */

		$sottocontiTable = "";
		
		if (sizeof($sottoconto->getNuoviSottoconti()) > 0) {

			$sottocontiTable =
			"<thead>" .
			"<tr>" .
			"<th width='100' align='center'>Sottoconto</th>" .
			"<th width='400' align='left'>Descrizione</th>" .
			"<th width='18' >&nbsp;</th>" .
			"</tr>" .
			"</thead>" .
			"<tbody>";
			
			foreach ($sottoconto->getNuoviSottoconti() as $unSottoconto) {
					
				$sottocontiTable .=
				"<tr id='" . $unSottoconto[0] . "'>" .
				"<td width='110' align='center'>" . $unSottoconto[0] . "</td>" .
				"<td width='409' align='left'>" . $unSottoconto[1] . "</td>" .
				"<td width='25' id='icons'><a class='tooltip' onclick='cancellaSottocontoPagina(" . $unSottoconto[0] . ")'><li class='ui-state-default ui-corner-all' title='Cancella'><span class='ui-icon ui-icon-trash'></span></li></a></td>" .
				"</tr>";
			}
			
			$sottocontiTable .= "</tbody>";
			$class_dettagli = "datiCreateSottile";
		}
			
		$replace = array(
				'%titoloPagina%' => $this->getTitoloPagina(),
				'%azione%' => $this->getAzione(),
				'%confermaTip%' => $this->getConfermaTip(),
				'%codconto%' => $conto->getCodConto(),
				'%desconto%' => str_replace("'", "&apos;", $conto->getDesConto()),
				'%class_dettagli%' => $class_dettagli,
				'%sottocontiTable%' => $sottocontiTable
		);
	
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}

?>
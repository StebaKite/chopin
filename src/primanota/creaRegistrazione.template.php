<?php

require_once 'primanota.abstract.class.php';

class creaRegistrazioneTemplate extends primanotaAbstract {
	
	private static $pagina = "/primanota/creaRegistrazione.form.html";
	
	//-----------------------------------------------------------------------------

	function __construct() {
		self::$root = $_SERVER['DOCUMENT_ROOT'];
	}

	// Setters & Getters  ----------------------------------------------------------


	// template ------------------------------------------------

	public function inizializzaPagina() {}

	public function controlliLogici() {
		
		$esito = TRUE;
		return $esito;
	}
	
	public function displayPagina() {

		require_once 'database.class.php';
		require_once 'utility.class.php';
		
		// Template --------------------------------------------------------------

		$utility = new utility();
		$array = $utility->getConfig();

 		$form = self::$root . $array['template'] . self::$pagina;
		
		$replace = array(
			'%titoloPagina%' => $this->getTitoloPagina(),
			'%azione%' => $this->getAzione(),
			'%confermaTip%' => $this->getConfermaTip(),
			'%elenco_causali%' => $_SESSION['elenco_causali'],
			'%elenco_fornitori%' => $_SESSION['elenco_fornitori'],
			'%elenco_clienti%' => $_SESSION['elenco_clienti'],
			'%elenco_conti%' => $_SESSION['elenco_conti']
		);

		$utility = new utility();

		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);		
	}	
}	

?>

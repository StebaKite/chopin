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

// 		$db = new database();

// 		$listino = "<option value=''>";
// 		$medico = "<option value=''>";
// 		$laboratorio = "<option value=''>";

// 		//-------------------------------------------------------------
// 		$sql = "select idListino, descrizioneListino from paziente.listino";
// 		$result = $db->getData($sql);
// 		while ($row = pg_fetch_row($result)) {
// 			if ($paziente->getListino() == $row[0])
// 				$listino = $listino . "<option value='$row[0]' selected>$row[1]";
// 			else
// 				$listino = $listino . "<option value='$row[0]'>$row[1]";
// 		}
		//-------------------------------------------------------------
		
		$replace = array(
			'%titoloPagina%' => $this->getTitoloPagina(),
			'%azione%' => $this->getAzione(),
			'%confermaTip%' => $this->getConfermaTip()
		);

		$utility = new utility();

		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);		
	}	
}	

?>

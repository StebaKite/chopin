<?php

require_once 'chopin.abstract.class.php';

class CorpoTemplate extends ChopinAbstract {

	public static $root;
	public static $pagina = "/main/corpo.form.html";

	private static $_instance = null;

	function __construct() {

		self::$root = $_SERVER['DOCUMENT_ROOT'];

		require_once 'utility.class.php';

		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		self::$testata = self::$root . $array['testataPagina'];
		self::$piede = self::$root . $array['piedePagina'];
		self::$messaggioErrore = self::$root . $array['messaggioErrore'];
		self::$messaggioInfo = self::$root . $array['messaggioInfo'];
	}

	private function  __clone() { }

	/**
	 * Singleton Pattern
	 */

	public static function getInstance() {

		if( !is_object(self::$_instance) )

			self::$_instance = new CorpoTemplate();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function displayPagina() {

		require_once 'database.class.php';
		require_once 'utility.class.php';
		
		$tabellaScadenze = "";
		
		// Template --------------------------------------------------------------

		$utility = Utility::getInstance();
		
		$array = $utility->getConfig();		
		$form = self::$root . $array['template'] . self::$pagina;
		
		if ($_SESSION["scadenzeMese"] != "") {
	
			$tabellaScadenze .= "<table class='result' id='resultTable'>";
			$tabellaScadenze .= "<thead>";
			$tabellaScadenze .= "<th nowrap>%ml.datascad%</th>";
			$tabellaScadenze .= "<th>%ml.desscad%</th>";
			$tabellaScadenze .= "<th>%ml.importo%</th>";
			$tabellaScadenze .= "</thead><tbody>";
				
			foreach($_SESSION["scadenzeMese"] as $row) {
				$tabellaScadenze .= "<tr class='off'>";
				$tabellaScadenze .= "<td width='50' align='center'>" . $row['dat_scadenza'] . "</td>";
				$tabellaScadenze .= "<td width='400' align='left'>" . $row['nota_scadenza'] . "</td>";
				$tabellaScadenze .= "<td width='100' align='right'>&euro;" . $row['imp_in_scadenza'] . "</td>";
				$tabellaScadenze .= "</tr>";
			}
	
			$tabellaScadenze .= "</tbody></table>";
		}
		
		$replace = array(
				'%risultato_scadenze%' => $tabellaScadenze
		);
		
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		
		echo $utility->tailTemplate($template);
		}
		
	}

?>
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
		
		$tabellaEventi = "";
		
		// Template --------------------------------------------------------------

		$utility = Utility::getInstance();
		
		$array = $utility->getConfig();		
		$form = self::$root . $array['template'] . self::$pagina;
		
		if ($_SESSION["eventi"] != "") {
	
			$tabellaEventi .= "<table class='result' id='resultTable' cellpadding='5'>";
			$tabellaEventi .= "<tbody>";

			$oggi = date("d/m/Y");
			$oggi_piu_2gg = $this->sommaGiorniData($oggi, "/", 2);
			$oggi_piu_5gg = $this->sommaGiorniData($oggi, "/", 5);
				
				
			foreach($_SESSION["eventi"] as $row) {
				
				if ($row['sta_evento'] == '00') {
					$class = "class='eventoOn'";
					$bottoneChiudi = "<a class='tooltip' href='../main/chiudiEventoFacade.class.php?modo=go&idevento=" . trim($row['id_evento']) . "&staevento=01" . "'><li class='ui-state-default ui-corner-all' title='%ml.chiudiEvento%'><span class='ui-icon ui-icon-check'></span></li></a>";

					if ($row['dat_evento'] <= $oggi) $class = "class='eventoUrgente'";
					
					if (($row['dat_evento'] <= $oggi_piu_2gg) && ($row['dat_evento'] > $oggi)) {
						$class = "class='eventoAttenzione'";
					}
					
					if (($row['dat_evento'] <= $oggi_piu_5gg) && ($row['dat_evento'] > $oggi_piu_2gg) ) {
						$class = "class='eventoGuarda'";
					}
						
				}
				else {
					$class = "class='eventoOff'";
					$bottoneChiudi = "&nbsp;";						
				}
				
				$tabellaEventi .= "<tr " . $class . " height='40'>";
				$tabellaEventi .= "<td width='50' align='center'>" . $row['dat_evento'] . "</td>";
				$tabellaEventi .= "<td width='780' align='left'>" . $row['nota_evento'] . "</td>";
				$tabellaEventi .= "<td width='50' align='center'>" . $row['dat_cambio_stato'] . "</td>";
				$tabellaEventi .= "<td width='30' id='icons'>" . $bottoneChiudi . "</td>";
				$tabellaEventi .= "</tr>";
			}
	
			$tabellaEventi .= "</tbody></table>";
		}
		else {
			$tabellaEventi = "<br>Nessun evento trovato" ;
		}
		
		$replace = array(
				'%aperti_checked%' => ($_SESSION["statoeventi"] == "00") ? "checked" : "",
				'%chiusi_checked%' => ($_SESSION["statoeventi"] == "01") ? "checked" : "",
				'%tutti_checked%'  => ($_SESSION["statoeventi"] == "")   ? "checked" : "",
				'%risultato_eventi%' => $tabellaEventi
		);
		
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		
		echo $utility->tailTemplate($template);
		}
		
	}

?>
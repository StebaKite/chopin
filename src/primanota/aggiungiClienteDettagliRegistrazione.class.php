<?php

require_once 'primanota.abstract.class.php';

class AggiungiClienteDettagliRegistrazione extends PrimanotaAbstract {

	public static $replace;

	private static $_instance = null;

	function __construct() {

		self::$root = $_SERVER['DOCUMENT_ROOT'];

		require_once 'utility.class.php';

		$utility = Utility::getInstance();
		$array = $utility->getConfig();
	}

	private function  __clone() { }

	/**
	 * Singleton Pattern
	 */

	public static function getInstance() {

		if( !is_object(self::$_instance) )

			self::$_instance = new AggiungiClienteDettagliRegistrazione();

			return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {

		require_once 'database.class.php';
		require_once 'utility.class.php';

		$db = Database::getInstance();
		$utility = Utility::getInstance();

		$array = $utility->getConfig();

		/**
		 * Prepara la tabella dei dettagli inseriti
		 */
			
		if ($_SESSION['dettagliInseriti'] != "") {

			$tabella_dettagli =
			"<table id='dettagli' class='display'>" .
			"<thead>" .
			"<tr>" .
			"<th>Conto</th>" .
			"<th class='dt-right'>Importo</th>" .
			"<th>D/A</th>" .
			"<th>&nbsp;</th>" .
			"</tr>" .
			"</thead>";

			$tabella_dettagli .= "<tbody>";

			if ($_SESSION['descliente'] != "") {
					
				$idconto = $this->prelevaContoCliente($db, $utility, $_SESSION['descliente']);
					
				if ($idconto != "") {

					$tabella_dettagli .=
					"<tr id='" . trim($idconto) . "'>" .
					"<td>" . $_SESSION['descliente'] .
					"<input type='hidden' id='conto' name='conto' value='" . trim($idconto) . "' />" .
					"<input type='hidden' id='desconto' name='desconto' value='" . $_SESSION['descliente'] . "' />" .
					"</td>" .
					"<td class='dt-right'>" .
					"<input type='text' id='importo' name='importo' size='10' maxlength='10' />" .
					"</td>" .
					"<td class='dt-center'>" .
					"<input type='text' id='segno' name='segno' size='2' maxlength='1' />" .
					"</td>" .
					"<td id='icons'><a class='tooltip' onclick='cancellaDettaglioPagina(" . trim($idconto) . ")'><li class='ui-state-default ui-corner-all' title='Cancella'><span class='ui-icon ui-icon-trash'></span></li></a></td>" .
					"</tr>";
				}
			}
				
			$dettagli = $_SESSION['dettagliInseriti'];

			foreach($dettagli as $det) {

				$idconto = $det[0] . $det[1];

				$tabella_dettagli .=
				"<tr id='" . trim($idconto) . "'>" .
				"<td>" . $det[2] .
				"<input type='hidden' id='conto' name='conto' value='" . trim($idconto) . "' />" .
				"<input type='hidden' id='desconto' name='desconto' value='" . $det[2] . "' />" .
				"</td>" .
				"<td class='dt-right'>" .
				"<input type='text' id='importo' name='importo' size='10' maxlength='10' />" .
				"</td>" .
				"<td class='dt-center'>" .
				"<input type='text' id='segno' name='segno' size='2' maxlength='1' />" .
				"</td>" .
				"<td id='icons'><a class='tooltip' onclick='cancellaDettaglioPagina(" . trim($idconto) . ")'><li class='ui-state-default ui-corner-all' title='Cancella'><span class='ui-icon ui-icon-trash'></span></li></a></td>" .
				"</tr>";
			}

			$tabella_dettagli .= "</tbody></table>";
			echo $tabella_dettagli;
		}
	}

	private function prelevaContoCliente($db, $utility, $descliente) {

		$db->beginTransaction();
		$cod_cliente = "";

		// prelevo l'ID del cliente accedendo con la descrizione
		$idcliente = $this->leggiDescrizioneCliente($db, $utility, str_replace("'", "''", $descliente));

		// prelevo il cliente accedendo con l'ID ricavato
		$result = $this->leggiIdCliente($db, $utility, $idcliente);

		foreach(pg_fetch_all($result) as $row) {
			$cod_cliente = $row['cod_cliente'];
		}

		// prelevo il capoconto

		$result = $this->leggiContoCliente($db, $utility, $cod_cliente);

		foreach(pg_fetch_all($result) as $row) {
			$conto_cliente = $row['cod_conto'];
		}

		$db->commitTransaction();

		// se non trovassi il fornitore restituisce una stringa vuota
		return $conto_cliente . $cod_cliente;
	}
}

?>
<?php

require_once 'chopin.abstract.class.php';

abstract class FatturaAbstract extends ChopinAbstract {

	public static $mese = array(
			'01' => 'Gennaio',
			'02' => 'Febbraio',
			'03' => 'Marzo',
			'04' => 'Aprile',
			'05' => 'Maggio',
			'06' => 'Giugno',
			'07' => 'Luglio',
			'08' => 'Agosto',
			'09' => 'Settembre',
			'10' => 'Ottobre',
			'11' => 'Novembre',
			'12' => 'Dicembre'
	);

	public static $anno;
	public static $nmese;
	public static $giorno;
	public static $meserif;
	public static $mesenome;
	
	public static $messaggio;

	// Query ---------------------------------------------------------------

 	public static $queryRicercaClienti = "/fatture/ricercaClienti.sql";
 	public static $queryRicercaNumeroFattura = "/fatture/ricercaNumeroFattura.sql";
 	public static $queryRicercaDatiCliente = "/fatture/ricercaDatiCliente.sql";
	public static $queryAggiornaNumeroFattura = "/fatture/aggiornaNumeroFattura.sql";
 	
	function __construct() {
	}

	private function  __clone() { }

	/**
	 * Singleton Pattern
	 */

	public static function getInstance() {

		if( !is_object(self::$_instance) )

			self::$_instance = new FatturaAbstract();

		return self::$_instance;
	}

	// Getters e Setters ---------------------------------------------------

	public function setMessaggio($messaggio) {
		self::$messaggio = $messaggio;
	}

	// ------------------------------------------------

	public function getMessaggio() {
		return self::$messaggio;
	}

	// Metodi comuni di utilita della prima note ---------------------------

	/**
	 * Questo metodo carica tutti i clienti fatturabili di una certa categoria 
	 * 
	 * @param unknown $utility
	 * @param unknown $db
	 * @param unknown $categoriaCliente
	 * @return string
	 */
	public function caricaClientiFatturabili($utility, $db, $categoriaCliente) {

		$array = $utility->getConfig();
		$replace = array(
				'%cat_cliente%' => trim($categoriaCliente)
		);
		
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaClienti;		
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);

		/**
		 * Prepara un elenco da inserire in una array javascript adatta per un campo autocomplete
		 */
		foreach(pg_fetch_all($result) as $row) {
			$elencoClienti .= '"' . $row["des_cliente"] . '",';
		}
		return $elencoClienti;
	}
	
	/**
	 * Questo metodo preleva l'ultimo progressivo fattura utilizzato
	 * 
	 * @param unknown $utility
	 * @param unknown $db
	 * @param unknown $categoriaCliente
	 * @param unknown $codiceNegozio
	 * @return un progressivo fattura utilizzabile
	 */
	public function caricaNumeroFattura($utility, $db, $categoriaCliente, $codiceNegozio) {

		$array = $utility->getConfig();
		$replace = array(
				'%cat_cliente%' => trim($categoriaCliente),
				'%neg_progr%' => trim($codiceNegozio)
		);
		
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaNumeroFattura;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		
		foreach(pg_fetch_all($result) as $row) {
			$numeroFattura = $row['num_fattura_ultimo'];
			$_SESSION["nota_testa_fattura"] = $row["nota_testa_fattura"];
			$_SESSION["nota_piede_fattura"] = $row["nota_piede_fattura"];
		}
		return $numeroFattura;
	}
	
	/**
	 * Questo metodo preleva il tipo addebito di un cliente
	 * 
	 * @param unknown $utility
	 * @param unknown $db
	 * @param unknown $idcliente
	 */
	public function caricaTipoAddebitoCliente($utility, $db, $idcliente) {

		$tipoAddebito = "";
		$array = $utility->getConfig();
		$replace = array(
				'%id_cliente%' => trim($idcliente),
		);
		
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaDatiCliente;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		
		if ($result) {
			foreach(pg_fetch_all($result) as $row) {
				$tipoAddebito = trim($row['tip_addebito']);
				
				$_SESSION["descliente"] = trim($row['des_cliente']);
				$_SESSION["indirizzocliente"] = trim($row['des_indirizzo_cliente']);
				$_SESSION["cittacliente"] = trim($row['des_citta_cliente']);
				$_SESSION["capcliente"] = trim($row['cap_cliente']);
				$_SESSION["pivacliente"] = trim($row['cod_piva']);
				$_SESSION["cfiscliente"] = trim($row['cod_fisc']);
			}
		}
		return $tipoAddebito;		
	}
	
	public function aggiornaNumeroFattura($utility, $db, $categoriaCliente, $codiceNegozio, $numeroFattura) {

		$array = $utility->getConfig();
		$replace = array(
				'%cat_cliente%' => trim($categoriaCliente),
				'%neg_progr%' => trim($codiceNegozio),
				'%num_fattura_ultimo%' => trim($numeroFattura)
		);
		
		$sqlTemplate = self::$root . $array['query'] . self::$queryAggiornaNumeroFattura;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		
		return $result;
	}

	public function intestazione($fattura) {
	
		$_SESSION["title"] = iconv('UTF-8', 'windows-1252', "Cooperativa Chopin - Cooperativa sociale - ONLUS");
		$_SESSION["title1"] = iconv('UTF-8', 'windows-1252', "Diversamente Impresa: Esperienza occupazionale-lavorativa");
		$_SESSION["title2"] = iconv('UTF-8', 'windows-1252', "Domicilio fiscale: via San Martirio, 1 - 24030 Villa d'Adda (BG) - C.F./P.IVA: 03691430163");
	
		return $fattura;
	}
	
	public function sezioneNotaPiede($fattura) {
	
		if (isset($_SESSION["nota_piede_fattura"])) {
			$nota = explode("\\", $_SESSION["nota_piede_fattura"]);
		}
		$fattura->aggiungiLineaNota($nota, 12, 242);
		return $fattura;
	}

	public function sezionePagamento($fattura) {
		$fattura->AddPage();
		$fattura->pagamento($_SESSION["tipoadd"]);
		return $fattura;
	}
	
	public function sezioneBanca($fattura) {
		$fattura->banca($_SESSION["ragsocbanca"], $_SESSION["ibanbanca"]);
		return $fattura;
	}
	
	public function sezioneDestinatario($fattura) {
		$fattura->destinatario($_SESSION["descliente"], $_SESSION["indirizzocliente"], $_SESSION["cittacliente"], $_SESSION["capcliente"], $_SESSION["pivacliente"], $_SESSION["cfiscliente"], $_SESSION["titolo"]);
		return $fattura;
	}
	
}

?>
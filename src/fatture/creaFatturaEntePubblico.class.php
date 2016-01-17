<?php

require_once 'fattura.abstract.class.php';

/**
 * Crazione della fattura per gli Enti Pubblici
 * 
 * @author stefano
 *
 */
class CreaFatturaEntePubblico extends FatturaAbstract {

	public static $mese = array(
			'01' => 'gennaio',
			'02' => 'febbraio',
			'03' => 'marzo',
			'04' => 'aprile',
			'05' => 'maggio',
			'06' => 'giugno',
			'07' => 'luglio',
			'08' => 'agosto',
			'09' => 'settembre',
			'10' => 'ottobre',
			'11' => 'novembre',
			'12' => 'dicembre'
	);
	
	public static $anno;
	public static $nmese;
	public static $giorno;
	public static $meserif;
	
	private static $_instance = null;
	
	public static $azioneCreaFatturaEntePubblico = "../fatture/creaFatturaEntePubblicoFacade.class.php?modo=go";
	
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
	
			self::$_instance = new CreaFatturaEntePubblico();
	
		return self::$_instance;
	}
	
	// ------------------------------------------------
	
	public function start() {
	
		require_once 'creaFatturaEntePubblico.template.php';
	
		$creaFatturaEntePubblicoTemplate = CreaFatturaEntePubblicoTemplate::getInstance();
		
		$_SESSION["datafat"] = date("d/m/Y");
		$_SESSION["idcliente"] = "";
		$_SESSION["codneg"] = "";
		$_SESSION["numfat"] = "";
		$_SESSION["tipoadd"] = "";
		$_SESSION["ragsocbanca"] = "";
		$_SESSION["ibanbanca"] = "";
		$_SESSION["tipofat"] = "";
		$_SESSION["dettagliInseriti"] = "";
		$_SESSION["indexDettagliInseriti"] = "";
		
		$this->preparaPagina($creaFatturaEntePubblicoTemplate);
		
		/**
		 * Compongo la pagina
		 */ 
		
		include(self::$testata);
		$creaFatturaEntePubblicoTemplate->displayPagina();
		include(self::$piede);
	}
	
	public function go() {
		
		require_once 'creaFatturaEntePubblico.template.php';
		require_once 'utility.class.php';
		require_once 'fatturaEntePubblico.class.php';		
		require_once 'database.class.php';
		
		// Creo la fattura -------------------------
		
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		$_SESSION["logo"] = self::$root . $array["logo"];
		$_SESSION["creator"] = "Nexus6";
		
		$fattura = FatturaEntePubblico::getInstance();
		
		$fattura->AliasNbPages();
		
		self::$anno = substr($_SESSION["datafat"], 6);
		self::$nmese = substr($_SESSION["datafat"], 3,2);
		self::$giorno = substr($_SESSION["datafat"], 0,2);
		$mm = str_pad(self::$nmese, 2, "0", STR_PAD_LEFT);
		self::$meserif = self::$mese[$mm];

		/**
		 * Aggiorno il numero fattura per l'ente pubblico e negozio
		 */
		
		$db = Database::getInstance();
		
		if ($this->aggiornaNumeroFattura($utility, $db, "1300", $_SESSION["codneg"], $_SESSION["numfat"])) {

			/**
			 * Generazione del documento
			 */
			
			$fattura = $this->intestazione($fattura);
			$fattura = $this->sezionePagamento($fattura);
			$fattura = $this->sezioneBanca($fattura);
			$fattura = $this->sezioneDestinatario($fattura);
			$fattura = $this->sezioneIdentificativiFattura($fattura);
			
			if ($_SESSION["tipofat"] == "CONTRIBUTO") { 
				$fattura = $this->sezioneNotaTesta($fattura);
				$fattura = $this->sezioneDettagliFattura($fattura, self::$meserif, 15, 180);			
				$fattura = $this->sezioneNotaPiede($fattura);
				$fattura = $this->sezioneTotaliContributo($fattura);
			}
			else {
				$fattura = $this->sezioneDettagliFattura($fattura, self::$meserif, 15, 120);
				$fattura = $this->sezioneTotaliVendita($fattura);
			}
			
			
			$fattura->Output();				
		}
			
		$creaFatturaEntePubblicoTemplate = CreaFatturaEntePubblicoTemplate::getInstance();
		$this->preparaPagina($creaFatturaEntePubblicoTemplate);
		
		include(self::$testata);
		$creaFatturaEntePubblicoTemplate->displayPagina();		
		include(self::$piede);
	}
	
	private function intestazione($fattura) {

		$_SESSION["title"] = "Cooperativa Chopin - Cooperativa sociale - ONLUS";
		$_SESSION["title1"] = "Diversamente Impresa: Esperienza occupazionale-lavorativa";
		$_SESSION["title2"] = utf8_decode("Domicilio fiscale: via San Martirio, 1 - 24030 Villa d'Adda (BG) - C.F./P.IVA: 03691430163");
		
		return $fattura;		
	}
	
	private function sezionePagamento($fattura) {
		$fattura->AddPage();
		$fattura->pagamento($_SESSION["tipoadd"]);		
		return $fattura;
	}	

	private function sezioneBanca($fattura) {	
		$fattura->banca($_SESSION["ragsocbanca"], $_SESSION["ibanbanca"]);
		return $fattura;
	}
	
	private function sezioneDestinatario($fattura) {
		$fattura->destinatario($_SESSION["descliente"], $_SESSION["indirizzocliente"], $_SESSION["cittacliente"], $_SESSION["capcliente"], $_SESSION["pivacliente"], $_SESSION["cfiscliente"]);
		return $fattura;
	}
	
	private function sezioneIdentificativiFattura($fattura) {
		$fattura->identificativiFatturaEntePubblico(self::$giorno, self::$meserif, self::$anno, $_SESSION["numfat"], $_SESSION["codneg"]);
		return $fattura;		
	}

	private function sezioneNotaTesta($fattura) {

		if (isset($_SESSION["nota_testa_fattura"])) {
			$nota = explode("\\", $_SESSION["nota_testa_fattura"]);
		}
		$fattura->aggiungiLineaNota($nota, 15, 120);
		return $fattura;		
	}

	private function sezioneNotaPiede($fattura) {
	
		if (isset($_SESSION["nota_piede_fattura"])) {
			$nota = explode("\\", $_SESSION["nota_piede_fattura"]);
		}
		$fattura->aggiungiLineaNota($nota, 12, 242);
		return $fattura;
	}
	
	private function sezioneDettagliFattura($fattura, $meserif, $r1, $y1) {

		$fattura->boxDettagli();
		
		$d = explode(",", $_SESSION['dettagliInseriti']);
			
		$tot_imponibile = 0;
		$tot_iva = 0;
		$w = array(125, 30, 30);
		
		for($i=0;$i<count($h);$i++)
			$fattura->Cell($w[$i],7,$h[$i],1,0,'C');
	
			$fattura->Ln();
	
			foreach($d as $ele) {
	
			$e = explode("#",$ele);
						
			$linea = array( "QUANTITA"   => $e[1],
					"ARTICOLO"	 => $e[2],
					"IMPORTO U." => $e[3],
					"TOTALE"     => $e[4],
					"IMPONIBILE" => $e[5],
					"IVA"        => $e[6],
					"%IVA"       => $e[7]
			);
	
			$fattura->aggiungiLineaLiberaEntePubblico($w, $linea, $r1, $y1);
	
			$tot_dettagli += $e[4];
			$tot_imponibile += $e[5];
			$tot_iva = $e[6];
			$aliq_iva = $e[7];
		}	
		
		$_SESSION["tot_dettagli"] = $tot_dettagli;
		$_SESSION["tot_imponibile"] = $tot_imponibile;
		$_SESSION["tot_iva"] = $tot_iva;	
		$_SESSION["aliquota_iva"] = $aliq_iva;

		/**
		 * Closing line
		 */
		
		$r1  = 10;
		$r2  = $r1 + 192;
		$y1  = 240;
		$fattura->Line( $r1, $y1, $r2, $y1);
		
		return $fattura;
	}
	
	public function sezioneTotaliVendita($fattura) {
		$fattura->totaliFatturaVenditaEntePubblico($_SESSION["tot_dettagli"], $_SESSION["tot_imponibile"], $_SESSION["tot_iva"], $_SESSION["aliquota_iva"]);
		return $fattura;
	}

	public function sezioneTotaliContributo($fattura) {
		$fattura->totaliFatturaContributoEntePubblico($_SESSION["tot_dettagli"], $_SESSION["tot_imponibile"], $_SESSION["tot_iva"], $_SESSION["aliquota_iva"]);
		return $fattura;
	}
	
	public function preparaPagina($creaFatturaEntePubblicoTemplate) {
	
		require_once 'database.class.php';
		require_once 'utility.class.php';

		$creaFatturaEntePubblicoTemplate->setAzione(self::$azioneCreaFatturaEntePubblico);
		$creaFatturaEntePubblicoTemplate->setConfermaTip("%ml.confermaCreaFattura%");
		$creaFatturaEntePubblicoTemplate->setTitoloPagina("%ml.creaFatturaEntePubblico%");
		
		$db = Database::getInstance();
		$utility = Utility::getInstance();
		
		// Prelievo delle aziende consortili -------------------------------------------------------------
		
		$_SESSION['elenco_clienti'] = $this->caricaClientiFatturabili($utility, $db, "1300");	// Categoria=1300 -> Enti
	}
}	

?>

<?php

require_once 'fattura.abstract.class.php';

/**
 * Crazione della fattura per le aziende consortili
 * 
 * @author stefano
 *
 */
class CreaFatturaAziendaConsortile extends FatturaAbstract {

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
	
	public static $azioneCreaFatturaAziendaConsortile = "../fatture/creaFatturaAziendaConsortileFacade.class.php?modo=go";
	
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
	
			self::$_instance = new CreaFatturaAziendaConsortile();
	
		return self::$_instance;
	}
	
	// ------------------------------------------------
	
	public function start() {
	
		require_once 'creaFatturaAziendaConsortile.template.php';
	
		$creaFatturaAziendaConsortileTemplate = CreaFatturaAziendaConsortileTemplate::getInstance();
		
		$_SESSION["datafat"] = date("d/m/Y");
		$_SESSION["idcliente"] = "";
		$_SESSION["codneg"] = "";
		$_SESSION["numfat"] = "";
		$_SESSION["tipoadd"] = "";
		$_SESSION["ragsocbanca"] = "";
		$_SESSION["ibanbanca"] = "";
		$_SESSION["dettagliInseriti"] = "";
		$_SESSION["indexDettagliInseriti"] = "";
		
		$this->preparaPagina($creaFatturaAziendaConsortileTemplate);
		
		/**
		 * Compongo la pagina
		 */ 
		
		include(self::$testata);
		$creaFatturaAziendaConsortileTemplate->displayPagina();
		include(self::$piede);
	}
	
	public function go() {
		
		require_once 'creaFatturaAziendaConsortile.template.php';
		require_once 'utility.class.php';
		require_once 'fatturaAziendaConsortile.class.php';		
		require_once 'database.class.php';
		
		// Creo la fattura -------------------------
		
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		$_SESSION["logo"] = self::$root . $array["logo"];
		$_SESSION["creator"] = "Nexus6";
		
		$fattura = FatturaAziendaConsortile::getInstance();
		
		$fattura->AliasNbPages();
		
		self::$anno = substr($_SESSION["datafat"], 6);
		self::$nmese = substr($_SESSION["datafat"], 3,2);
		self::$giorno = substr($_SESSION["datafat"], 0,2);
		$mm = str_pad(self::$nmese, 2, "0", STR_PAD_LEFT);
		self::$meserif = self::$mese[$mm];

		/**
		 * Aggiorno il numero fattura per l'azienda consortile e negozio
		 */
		
		$db = Database::getInstance();
		
		if ($this->aggiornaNumeroFattura($utility, $db, "1200", $_SESSION["codneg"], $_SESSION["numfat"])) {

			/**
			 * Generazione del documento
			 */
			
			$fattura = $this->intestazione($fattura);
			$fattura = $this->sezionePagamento($fattura);
			$fattura = $this->sezioneBanca($fattura);
			$fattura = $this->sezioneDestinatario($fattura);
			$fattura = $this->sezioneIdentificativiFattura($fattura);
			$fattura = $this->sezioneDettagliFattura($fattura, self::$meserif);
			$fattura = $this->sezioneTotali($fattura);
			
			$fattura->Output();				
		}
			
		$creaFatturaAziendaConsortileTemplate = CreaFatturaAziendaConsortileTemplate::getInstance();
		$this->preparaPagina($creaFatturaAziendaConsortileTemplate);
		
		include(self::$testata);
		$creaFatturaAziendaConsortileTemplate->displayPagina();
		
		self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
		$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
		echo $utility->tailTemplate($template);
		
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
		$fattura->identificativiFatturaAziendaConsortile(self::$giorno, self::$meserif, self::$anno, $_SESSION["numfat"], $_SESSION["codneg"]);
		return $fattura;		
	}

	private function sezioneDettagliFattura($fattura, $meserif) {

		$fattura->boxDettagli();
		
		$d = explode(",", $_SESSION['dettagliInseriti']);
			
		$tot_imponibile = 0;
		$tot_iva = 0;
		$w = array(15, 110, 30, 30);
			
		$fattura->SetXY( 15, 120 );		
		$fattura->SetFont( "Arial", "B", 10);
		$fattura->Cell(50,6,"Mese di " . $meserif, "");
		$fattura->Ln();
		
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
					"IVA"        => $e[6]
			);
	
			$fattura->aggiungiLineaLiberaAziendaConsortile($w, $linea);
	
			$tot_dettagli += $e[4];
			$tot_imponibile += $e[5];
			$tot_iva += $e[6];
		}	
		
		$_SESSION["tot_dettagli"] = $tot_dettagli;
		$_SESSION["tot_imponibile"] = $tot_imponibile;
		$_SESSION["tot_iva"] = $tot_iva;	

		/**
		 * Closing line
		 */
		
		$r1  = 10;
		$r2  = $r1 + 192;
		$y1  = 240;
		$fattura->Line( $r1, $y1, $r2, $y1);
		
		return $fattura;
	}
	
	public function sezioneTotali($fattura) {
		$fattura->totaliFatturaAziendaConsortile($_SESSION["tot_dettagli"], $_SESSION["tot_imponibile"], $_SESSION["tot_iva"]);
		return $fattura;
	}
	
	public function preparaPagina($creaFatturaAziendaConsortileTemplate) {
	
		require_once 'database.class.php';
		require_once 'utility.class.php';

		$creaFatturaAziendaConsortileTemplate->setAzione(self::$azioneCreaFatturaAziendaConsortile);
		$creaFatturaAziendaConsortileTemplate->setConfermaTip("%ml.confermaCreaFattura%");
		$creaFatturaAziendaConsortileTemplate->setTitoloPagina("%ml.creaFatturaAziendaConsortile%");
		
		$db = Database::getInstance();
		$utility = Utility::getInstance();
		
		// Prelievo delle aziende consortili -------------------------------------------------------------
		
		$_SESSION['elenco_clienti'] = $this->caricaClientiFatturabili($utility, $db, "1200");	// Categoria=1200 -> Aziende
	}
}	

?>
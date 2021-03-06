<?php

require_once 'fattura.abstract.class.php';

/**
 * Crazione della fattura per i Clienti / Famiglie
 * 
 * @author stefano
 *
 */
class CreaFatturaCliente extends FatturaAbstract {

	public static $_instance = null;
	
	public static $azioneCreaFatturaCliente = "../fatture/creaFatturaClienteFacade.class.php?modo=go";
	
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
	
			self::$_instance = new CreaFatturaCliente();
	
		return self::$_instance;
	}
	
	// ------------------------------------------------
	
	public function start() {
	
		require_once 'creaFatturaCliente.template.php';
		require_once 'utility.class.php';
		
		$utility = Utility::getInstance();
		
		$creaFatturaClienteTemplate = CreaFatturaClienteTemplate::getInstance();
		
		$_SESSION["datafat"] = date("d/m/Y");
		unset($_SESSION["cliente"]);
		unset($_SESSION["descli"]);
		unset($_SESSION["codneg"]);
		unset($_SESSION["numfat"]);
		unset($_SESSION["tipoadd"]);
		unset($_SESSION["ragsocbanca"]);
		unset($_SESSION["ibanbanca"]);
		unset($_SESSION["tipofat"]);
		unset($_SESSION["cognomenomeassistito"]);
		unset($_SESSION["dettagliInseriti"]);
		unset($_SESSION["indexDettagliInseriti"]);
		
		$this->preparaPagina($creaFatturaClienteTemplate);
		
		/**
		 * Compongo la pagina
		 */ 
		
		$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
		$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
		echo $utility->tailTemplate($template);

		$creaFatturaClienteTemplate->displayPagina();
		include(self::$piede);
	}
	
	public function go() {
		
		require_once 'creaFatturaCliente.template.php';
		require_once 'utility.class.php';
		require_once 'fatturaCliente.class.php';		
		require_once 'database.class.php';
		
		// Creo la fattura -------------------------
		
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		$_SESSION["logo"] = self::$root . $array["logo"];
		$_SESSION["creator"] = "Nexus6";
		
		$fattura = FatturaCliente::getInstance();
		
		$fattura->AliasNbPages();
		
		self::$anno = substr($_SESSION["datafat"], 6);
		self::$nmese = substr($_SESSION["datafat"], 3,2);
		self::$giorno = substr($_SESSION["datafat"], 0,2);
		$mm = str_pad(self::$nmese, 2, "0", STR_PAD_LEFT);
		self::$meserif = self::$mese[$mm];

		/**
		 * Aggiorno il numero fattura per il cliente e negozio
		 */
		
		$db = Database::getInstance();
		
		if ($this->aggiornaNumeroFattura($utility, $db, "1000", $_SESSION["codneg"], $_SESSION["numfat"])) {

			/**
			 * Generazione del documento
			 */
			
			$fattura = $this->intestazione($fattura);
			$fattura = $this->sezionePagamento($fattura);
			$fattura = $this->sezioneBanca($fattura);
			$fattura = $this->sezioneDestinatario($fattura);
			$fattura = $this->sezioneIdentificativiFattura($fattura);
			
			if ($_SESSION["tipofat"] == "CONTRIBUTO") { 
				$fattura = $this->sezioneNotaTesta($fattura, $utility);
				$fattura = $this->sezioneDettagliFatturaContributo($fattura, self::$meserif, 15, 180);			
				$fattura = $this->sezioneNotaPiede($fattura);
				$fattura = $this->sezioneTotaliContributo($fattura);
			}
			else {
				$fattura = $this->sezioneDettagliFatturaVendita($fattura, self::$meserif, 15, 120);
				$fattura = $this->sezioneNotaPiede($fattura);
				$fattura = $this->sezioneTotaliVendita($fattura);
			}
			
			
			$fattura->Output();				
		}
			
		$creaFatturaClienteTemplate = CreaFatturaClienteTemplate::getInstance();
		$this->preparaPagina($creaFatturaClienteTemplate);
		
		$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
		$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
		echo $utility->tailTemplate($template);

		$creaFatturaClienteTemplate->displayPagina();		
		include(self::$piede);
	}
	
	private function sezioneIdentificativiFattura($fattura) {
		$fattura->identificativiFatturaCliente(self::$giorno, self::$meserif, self::$anno, $_SESSION["numfat"], $_SESSION["codneg"]);
		return $fattura;		
	}

	private function sezioneNotaTesta($fattura, $utility) {

		if (isset($_SESSION["nota_testa_fattura"])) {
			$nota = explode("\\", $_SESSION["nota_testa_fattura"]);
			
			$array = $utility->getConfig();
			$replace = array(
					'%ASSISTITO%' => trim($_SESSION["cognomenomeassistito"])
			);

			/**
			 * Cerco il placeholder negli spezzoni della nota
			 */
			$i = 0;
			foreach($nota as $spezzone) {
				$nota[$i] = $utility->tailFile($spezzone, $replace);
				$i++;				
			}			
		}
		$fattura->aggiungiLineaNota($nota, 15, 120);
		return $fattura;		
	}

	private function sezioneDettagliFatturaVendita($fattura) {
		
		$r1  = 10;
		$r2  = $r1 + 192;
		$y1  = 118;
		$fattura->SetDrawColor(204, 204, 204);
		
		$tot_imponibile_10 = 0;
		$tot_iva_10 = 0;
		$tot_imponibile_22 = 0;
		$tot_iva_22 = 0;		
		$tot_imponibile = 0;
		$tot_iva = 0;
		
		$fattura->boxDettagli();
		
		$d = explode(",", $_SESSION['dettagliInseriti']);
		
		$w = array(10, 82, 25, 20, 25, 15, 15);
		$h = array("QTA'", "DESCRIZIONE", "PREZZO U.", "TOTALE", "IMPONIBILE", "IVA", "C.IVA" );

		$fattura->Line( $r1, $y1, $r2, $y1);		// linea dopo intestazione

		/**
		 * Linee colonne
		 */
		$rc1 = 10;
		$yc1 = 240;
		$yc2 = 106;

		for($i=0;$i<count($w)-1;$i++) {
			$rc1 += $w[$i];
			$fattura->Line( $rc1, $yc1, $rc1, $yc2);
		}

		/**
		 * Intestazioni colonne
		 */
		$fattura->SetXY( $r1, $y1-10 );
		
		for($i=0;$i<count($h);$i++) {

			/**
			 * Allineamento intestazioni
			 */
			if ($h[$i] == "DESCRIZIONE") $align = "L";
			elseif ($h[$i] == "C.IVA") $align = "C";
			else $align = "R";
			
			$fattura->Cell($w[$i],7,$h[$i],"",0,$align);
		}
		
		/**
		 * Linee fattura
		 */
		$fattura->SetXY( $r1, $y1 );
		
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

			$fattura->aggiungiLineaTabella($w, $linea);

			/**
			 * Accumulo totali per aliquota iva
			 */
			
			if ($e[7] == "10") {
				$tot_imponibile_10 += $e[5];
				$tot_iva_10 += $e[6];
			}
			elseif ($e[7] == "22") {
				$tot_imponibile_22 += $e[5];
				$tot_iva_22 += $e[6];
			}	
			elseif ($e[7] == "5") {
				$tot_imponibile += $e[5];
				$tot_iva += $e[6];
			}	
		}

		/**
		 * Salvo in sessione i totali accumulati
		 */
		$_SESSION["tot_imponibile_10"] = $tot_imponibile_10;
		$_SESSION["tot_iva_10"] = $tot_iva_10;
		$_SESSION["tot_imponibile_22"] = $tot_imponibile_22;
		$_SESSION["tot_iva_22"] = $tot_iva_22;
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
	
	private function sezioneDettagliFatturaContributo($fattura, $meserif, $r1, $y1) {

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
					"ALIQUOTA"	 => $e[7]
			);
	
			$fattura->aggiungiLineaLiberaCliente($w, $linea, $r1, $y1);
	
			$tot_dettagli += $e[4];
			$tot_imponibile += $e[5];
			$tot_iva = $e[6];
			$aliquota_iva = $e[7];
		}	
		
		$_SESSION["tot_dettagli"] = $tot_dettagli;
		$_SESSION["tot_imponibile"] = $tot_imponibile;
		$_SESSION["tot_iva"] = $tot_iva;	
		$_SESSION["aliquota_iva"] = $aliquota_iva;
		
		/**
		 * Closing line
		 */
		
		$r1  = 10;
		$r2  = $r1 + 192;
		$y1  = 240;
		$fattura->Line( $r1, $y1, $r2, $y1);
		
		return $fattura;
	}
	
	public function sezioneTotaliContributo($fattura) {
		$fattura->totaliFatturaContributoCliente($_SESSION["tot_dettagli"], $_SESSION["tot_imponibile"], $_SESSION["tot_iva"], $_SESSION["aliquota_iva"]);
		return $fattura;
	}

	public function sezioneTotaliVendita($fattura) {
		$fattura->totaliFatturaVenditaCliente($_SESSION["tot_imponibile"], $_SESSION["tot_iva"], $_SESSION["tot_imponibile_10"], $_SESSION["tot_iva_10"], $_SESSION["tot_imponibile_22"], $_SESSION["tot_iva_22"]);
		return $fattura;
	}
	
	public function preparaPagina($creaFatturaClienteTemplate) {
	
		require_once 'database.class.php';
		require_once 'utility.class.php';

		$creaFatturaClienteTemplate->setAzione(self::$azioneCreaFatturaCliente);
		$creaFatturaClienteTemplate->setConfermaTip("%ml.confermaCreaFattura%");
		$creaFatturaClienteTemplate->setTitoloPagina("%ml.creaFatturaCliente%");
		
		$db = Database::getInstance();
		$utility = Utility::getInstance();
		
		// Prelievo delle aziende consortili -------------------------------------------------------------
		
		$_SESSION['elenco_clienti'] = $this->caricaClientiFatturabili($utility, $db, "1000");	// Categoria=1000 -> Cliente
	}
}	

?>
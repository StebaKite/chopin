<?php

require_once 'fattura.abstract.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'fattura.class.php';
require_once 'cliente.class.php';
require_once 'dettaglioFattura.class.php';
require_once 'creaFatturaAziendaConsortile.template.php';
require_once 'fatturaAziendaConsortile.class.php';
require_once 'fatture.business.interface.php';

/**
 * Crazione della fattura per le aziende consortili
 *
 * @author stefano
 *
 */
class CreaFatturaAziendaConsortile extends FatturaAbstract implements FattureBusinessInterface {

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();

        $this->testata = $this->root . $this->array[self::TESTATA];
        $this->piede = $this->root . $this->array[self::PIEDE];
        $this->messaggioErrore = $this->root . $this->array[self::ERRORE];
        $this->messaggioInfo = $this->root . $this->array[self::INFO];
    }

    public function getInstance() {

        if (!isset($_SESSION[self::CREA_FATTURA_AZIENDA_CONSORTILE]))
            $_SESSION[self::CREA_FATTURA_AZIENDA_CONSORTILE] = serialize(new CreaFatturaAziendaConsortile());
        return unserialize($_SESSION[self::CREA_FATTURA_AZIENDA_CONSORTILE]);
    }

    public function start() {

        $fattura = Fattura::getInstance();
        $dettaglioFattura = DettaglioFattura::getInstance();
        $utility = Utility::getInstance();
        $creaFatturaAziendaConsortileTemplate = CreaFatturaAziendaConsortileTemplate::getInstance();

        $fattura->prepara();
        $dettaglioFattura->prepara();
        $this->preparaPagina($creaFatturaAziendaConsortileTemplate);

        $replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array, $_SESSION), '%menu%' => $this->makeMenu($utility)));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);

        $creaFatturaAziendaConsortileTemplate->displayPagina();
        include($this->piede);
    }

    public function go() {

        $fattura = Fattura::getInstance();
        $dettaglioFattura = DettaglioFattura::getInstance();
        $cliente = Cliente::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $fatturaAziendaConsortile = FatturaAziendaConsortile::getInstance();
        $fatturaAziendaConsortile->initialize();
        $fatturaAziendaConsortile->setLogo($this->root . $array["logo"]);
        $fatturaAziendaConsortile->setCreator($array["productName"]);

        $fatturaAziendaConsortile->AliasNbPages();

        /**
         * Se il mese di riferimento in pagina non è stato selezionato lo ricavo dalla data fattura
         */
        $fattura->setAnno(substr($fattura->getDatFattura(), 6));
        $fattura->setNmese(substr($fattura->getDatFattura(), 3, 2));
        $fattura->setGiorno(substr($fattura->getDatFattura(), 0, 2));        
        $mm = str_pad($fattura->getNmese(), 2, "0", STR_PAD_LEFT);

        if (parent::isEmpty($fattura->getMeserif())) {
            $fattura->setMeserif($mm);
        }
        $fattura->setMesenome($fattura->getMese($fattura->getMeserif()));
        
        /**
         * Aggiorno il numero fattura per l'azienda consortile e negozio
         */
        $db = Database::getInstance();

        if ($fattura->aggiornaNumeroFattura($db)) {

            /**
             * Genero il documento
             */
            $fatturaAziendaConsortile = $this->intestazione($fatturaAziendaConsortile);
            $fatturaAziendaConsortile = $this->sezionePagamento($fatturaAziendaConsortile, $fattura);
            $fatturaAziendaConsortile = $this->sezioneBanca($fatturaAziendaConsortile, $fattura);
            $fatturaAziendaConsortile = $this->sezioneDestinatario($fatturaAziendaConsortile, $cliente, $fattura);
            $fatturaAziendaConsortile = $this->sezioneIdentificativiFattura($fatturaAziendaConsortile, $fattura);
            $fatturaAziendaConsortile = $this->sezioneDettagliFattura($fatturaAziendaConsortile, $fattura, $dettaglioFattura);
            $fatturaAziendaConsortile = $this->sezioneNotaPiede($fatturaAziendaConsortile, $fattura);

            $fatturaAziendaConsortile = $this->sezioneTotali($fatturaAziendaConsortile, $fattura);

            $fatturaAziendaConsortile->Output();
        }

        $creaFatturaAziendaConsortileTemplate = CreaFatturaAziendaConsortileTemplate::getInstance();
        $this->preparaPagina($creaFatturaAziendaConsortileTemplate);

        $replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array, $_SESSION), '%menu%' => $this->makeMenu($utility)));
        $template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
        echo $utility->tailTemplate($template);

        $creaFatturaAziendaConsortileTemplate->displayPagina();

        self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
        $template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
        echo $utility->tailTemplate($template);

        include(self::$piede);
    }

    private function sezioneIdentificativiFattura($fatturaAziendaConsortile, $fattura) {
        $fatturaAziendaConsortile->identificativiFatturaAziendaConsortile($fattura->getGiorno(), $fattura->getMeserif(), $fattura->getAnno(), $fattura->getNumFattura(), $fattura->getCodNegozio());
        return $fatturaAziendaConsortile;
    }

    private function sezioneDettagliFattura($fatturaAziendaConsortile, $fattura, $dettaglioFattura) {

        $fatturaAziendaConsortile->boxDettagli();

        $tot_imponibile = 0;
        $tot_iva = 0;
        $w = array(15, 110, 30, 30);

        $fatturaAziendaConsortile->SetXY(15, 120);
        $fatturaAziendaConsortile->SetFont("Arial", "B", 12);
        $fatturaAziendaConsortile->Cell(50, 6, "Mese di " . $fattura->getMesenome(), "");
        $fatturaAziendaConsortile->Ln();

        for ($i = 0; $i < count($h); $i++)
            $fatturaAziendaConsortile->Cell($w[$i], 7, $h[$i], 1, 0, 'C');

        $fatturaAziendaConsortile->Ln();

        foreach ($dettaglioFattura->getDettagliFattura() as $ele) {

            $linea = array("QUANTITA" => $ele[DettaglioFattura::QTA_ARTICOLO],
                "ARTICOLO" => $ele[DettaglioFattura::DES_ARTICOLO],
                "IMPORTO U." => $ele[DettaglioFattura::IMP_ARTICOLO],
                "TOTALE" => $ele[DettaglioFattura::IMP_TOTALE],
                "IMPONIBILE" => $ele[DettaglioFattura::IMP_IMPONIBILE],
                "IVA" => $ele[DettaglioFattura::IMP_IVA]
            );

            $fatturaAziendaConsortile->aggiungiLineaLiberaAziendaConsortile($w, $linea);

            $tot_dettagli += $ele[DettaglioFattura::IMP_TOTALE];
            $tot_imponibile += $ele[DettaglioFattura::IMP_IMPONIBILE];
            $tot_iva += $ele[DettaglioFattura::IMP_IVA];
        }

        $fattura->setTotDettagli($tot_dettagli);
        $fattura->setTotImponibile($tot_imponibile);
        $fattura->setTotIva($tot_iva);

        $_SESSION[self::FATTURA] = serialize($fattura);

        /**
         * Closing line
         */
        $r1 = 10;
        $r2 = $r1 + 192;
        $y1 = 240;
        $fatturaAziendaConsortile->Line($r1, $y1, $r2, $y1);

        return $fatturaAziendaConsortile;
    }

    public function sezioneTotali($fatturaAziendaConsortile, $fattura) {
        $fatturaAziendaConsortile->totaliFatturaAziendaConsortile($fattura->getTotDettagli(), $fattura->getTotImponibile(), $fattura->getTotIva());
        return $fatturaAziendaConsortile;
    }

    public function preparaPagina($creaFatturaAziendaConsortileTemplate) {

        $_SESSION[self::TITOLO_PAGINA] = "%ml.creaFatturaAziendaConsortile%";

        $db = Database::getInstance();
        $utility = Utility::getInstance();

        // Prelievo delle aziende consortili -------------------------------------------------------------

        $_SESSION['elenco_clienti'] = $this->caricaClientiFatturabili($utility, $db, "1200"); // Categoria=1200 -> Aziende
    }

}

?>
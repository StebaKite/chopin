<?php

require_once 'fattura.abstract.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'fattura.class.php';
require_once 'dettaglioFattura.class.php';
require_once 'creaFatturaEntePubblico.template.php';
require_once 'fatturaEntePubblico.class.php';
require_once 'cliente.class.php';
require_once 'fatture.business.interface.php';

/**
 * Crazione della fattura per gli Enti Pubblici
 *
 * @author stefano
 *
 */
class CreaFatturaEntePubblico extends FatturaAbstract implements FattureBusinessInterface {

//    public static $azioneCreaFatturaEntePubblico = "../fatture/creaFatturaEntePubblicoFacade.class.php?modo=go";

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

        if (!isset($_SESSION[self::CREA_FATTURA_ENTE_PUBBLICO]))
            $_SESSION[self::CREA_FATTURA_ENTE_PUBBLICO] = serialize(new CreaFatturaEntePubblico());
        return unserialize($_SESSION[self::CREA_FATTURA_ENTE_PUBBLICO]);
    }

    public function start() {

        $fattura = Fattura::getInstance();
        $dettaglioFattura = DettaglioFattura::getInstance();
        $utility = Utility::getInstance();
        $creaFatturaEntePubblicoTemplate = CreaFatturaEntePubblicoTemplate::getInstance();

        $fattura->prepara();
        $dettaglioFattura->prepara();
        $this->preparaPagina();

        $replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array, $_SESSION), '%menu%' => $this->makeMenu($utility)));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);

        $creaFatturaEntePubblicoTemplate->displayPagina();
        include($this->piede);
    }

    public function go() {

        $fattura = Fattura::getInstance();
        $dettaglioFattura = DettaglioFattura::getInstance();
        $cliente = Cliente::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $fatturaEntePubblico = FatturaEntePubblico::getInstance();
        $fatturaEntePubblico->initialize();
        $fatturaEntePubblico->setLogo($this->root . $array["logo"]);
        $fatturaEntePubblico->setCreator($array["productName"]);

        $fatturaEntePubblico->AliasNbPages();

        $fattura->setAnno(substr($fattura->getDatFattura(), 6));
        $fattura->setNmese(substr($fattura->getDatFattura(), 3, 2));
        $fattura->setGiorno(substr($fattura->getDatFattura(), 0, 2));
        $mm = str_pad($fattura->getNmese(), 2, "0", STR_PAD_LEFT);
        $fattura->setMeserif($mm);

        if (parent::isNotEmpty($fattura->getMeserif()))
            $fattura->setMesenome($fattura->getMese($fattura->getMeserif()));
        else
            $fattura->setMesenome($fattura->getMese($nm));

        /**
         * Aggiorno il numero fattura per l'ente pubblico e negozio
         */
        $db = Database::getInstance();

        if ($fattura->aggiornaNumeroFattura($db)) {

            /**
             * Generazione del documento
             */
            $fatturaEntePubblico = $this->intestazione($fatturaEntePubblico);
            $fatturaEntePubblico = $this->sezionePagamento($fatturaEntePubblico, $fattura);
            $fatturaEntePubblico = $this->sezioneBanca($fatturaEntePubblico, $fattura);
            $fatturaEntePubblico = $this->sezioneDestinatario($fatturaEntePubblico, $cliente, $fattura);
            $fatturaEntePubblico = $this->sezioneIdentificativiFattura($fatturaEntePubblico, $fattura);

            if ($fattura->getTipFattura() == self::CONTRIBUTO) {
                $fatturaEntePubblico = $this->sezioneNotaTesta($fatturaEntePubblico, $fattura);
                $fatturaEntePubblico = $this->sezioneDettagliFattura($fatturaEntePubblico, $fattura, $dettaglioFattura, 15, 180);
                $fatturaEntePubblico = $this->sezioneNotaPiede($fatturaEntePubblico, $fattura);
                $fatturaEntePubblico = $this->sezioneTotaliContributo($fatturaEntePubblico, $fattura);
            } else {
                $fatturaEntePubblico = $this->sezioneDettagliFattura($fatturaEntePubblico, $fattura, $dettaglioFattura, 15, 120);
                $fatturaEntePubblico = $this->sezioneTotaliVendita($fatturaEntePubblico, $fattura);
            }


            $fatturaEntePubblico->Output();
        }

        $creaFatturaEntePubblicoTemplate = CreaFatturaEntePubblicoTemplate::getInstance();
        $this->preparaPagina($creaFatturaEntePubblicoTemplate);

        $replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array, $_SESSION), '%menu%' => $this->makeMenu($utility)));
        $template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
        echo $utility->tailTemplate($template);

        $creaFatturaEntePubblicoTemplate->displayPagina();
        include(self::$piede);
    }

    private function sezioneIdentificativiFattura($documento, $fattura) {
        $documento->identificativiFatturaEntePubblico($fattura->getGiorno(), $fattura->getMeserif(), $fattura->getAnno(), $fattura->getNumFattura(), $fattura->getCodNegozio());
        return $documento;
    }

    private function sezioneNotaTesta($documento, $fattura) {

        if (parent::isNotEmpty($fattura->getNotaTesta())) {
            $nota = explode("\\", $fattura->getNotaTesta());
        }
        $documento->aggiungiLineaNota($nota, 15, 120);
        return $documento;
    }

    private function sezioneDettagliFattura($documento, $fattura, $dettaglioFattura, $r1, $y1) {

        $documento->boxDettagli();

        $tot_imponibile = 0;
        $tot_iva = 0;
        $w = array(125, 30, 30);

        for ($i = 0; $i < count($h); $i++)
            $documento->Cell($w[$i], 7, $h[$i], 1, 0, 'C');

        $documento->Ln();

        foreach ($dettaglioFattura->getDettagliFattura() as $ele) {

            $linea = array("QUANTITA" => $ele[DettaglioFattura::QTA_ARTICOLO],
                "ARTICOLO" => $ele[DettaglioFattura::DES_ARTICOLO],
                "IMPORTO U." => $ele[DettaglioFattura::IMP_ARTICOLO],
                "TOTALE" => $ele[DettaglioFattura::IMP_TOTALE],
                "IMPONIBILE" => $ele[DettaglioFattura::IMP_IMPONIBILE],
                "IVA" => $ele[DettaglioFattura::IMP_IVA],
                "%IVA" => $ele[DettaglioFattura::COD_ALIQUOTA]
            );

            $documento->aggiungiLineaLiberaEntePubblico($w, $linea, $r1, $y1);

            $tot_imponibile += $ele[DettaglioFattura::IMP_IMPONIBILE];
            $tot_iva += $ele[DettaglioFattura::IMP_IVA];
            $aliq_iva = $ele[DettaglioFattura::COD_ALIQUOTA];
            $tot_dettagli += $ele[DettaglioFattura::IMP_TOTALE];
        }

        $fattura->setTotImponibile($tot_imponibile);
        $fattura->setTotIva($tot_iva);
        $fattura->setTotDettagli($tot_dettagli);
        $fattura->setAliquotaIva($aliq_iva);

        $_SESSION[self::FATTURA] = serialize($fattura);

        /**
         * Closing line
         */
        $r1 = 10;
        $r2 = $r1 + 192;
        $y1 = 240;
        $documento->Line($r1, $y1, $r2, $y1);

        return $documento;
    }

    public function sezioneTotaliVendita($documento, $fattura) {
        $documento->totaliFatturaVenditaEntePubblico($fattura->getTotDettagli(), $fattura->getTotImponibile(), $fattura->getTotIva(), $fattura->getAliquotaIva());
        return $documento;
    }

    public function sezioneTotaliContributo($documento, $fattura) {
        $documento->totaliFatturaContributoEntePubblico($fattura->getTotDettagli(), $fattura->getTotImponibile(), $fattura->getTotIva(), $fattura->getAliquotaIva());
        return $documento;
    }

    public function preparaPagina() {

        $_SESSION[self::TITOLO_PAGINA] = "%ml.creaFatturaEntePubblico%";

        $db = Database::getInstance();
        $utility = Utility::getInstance();

        // Prelievo dei clienti -------------------------------------------------------------

        $_SESSION['elenco_clienti'] = $this->caricaClientiFatturabili($utility, $db, "1300"); // Categoria=1300 -> Enti
    }

}

?>

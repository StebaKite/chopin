<?php

require_once 'fattura.abstract.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'fattura.class.php';
require_once 'dettaglioFattura.class.php';
require_once 'creaFatturaCliente.template.php';
require_once 'fatturaCliente.class.php';
require_once 'cliente.class.php';
require_once 'fatture.business.interface.php';

/**
 * Crazione della fattura per i Clienti / Famiglie
 *
 * @author stefano
 *
 */
class CreaFatturaCliente extends FatturaAbstract implements FattureBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();

        $this->testata = $this->root . $this->array[self::TESTATA];
        $this->piede = $this->root . $this->array[self::PIEDE];
        $this->messaggioErrore = $this->root . $this->array[self::ERRORE];
        $this->messaggioInfo = $this->root . $this->array[self::INFO];
    }

    public static function getInstance() {

        if (parent::getIndexSession(self::CREA_FATTURA_CLIENTE) === NULL) {
            parent::setIndexSession(self::CREA_FATTURA_CLIENTE, serialize(new CreaFatturaCliente()));
        }
        return unserialize(parent::getIndexSession(self::CREA_FATTURA_CLIENTE));
    }

    public function start() {

        $fattura = Fattura::getInstance();
        $dettaglioFattura = DettaglioFattura::getInstance();
        $utility = Utility::getInstance();
        $creaFatturaClienteTemplate = CreaFatturaClienteTemplate::getInstance();

        $fattura->prepara();
        $dettaglioFattura->prepara();
        $this->preparaPagina();

        $replace = parent::getIndexSession(self::AMBIENTE) !== NULL ? array('%amb%' => parent::getIndexSession(self::AMBIENTE), '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array), '%menu%' => $this->makeMenu($utility));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);

        $creaFatturaClienteTemplate->displayPagina();
        include($this->piede);
    }

    public function go() {

        $fattura = Fattura::getInstance();
        $dettaglioFattura = DettaglioFattura::getInstance();
        $cliente = Cliente::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $fatturaCliente = FatturaCliente::getInstance();
        $fatturaCliente->initialize();
        $fatturaCliente->setLogo($this->root . $array["logo"]);
        $fatturaCliente->setCreator($array["productName"]);

        $fatturaCliente->AliasNbPages();

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
         * Aggiorno il numero fattura per il cliente e negozio
         */
        $db = Database::getInstance();

        if ($fattura->aggiornaNumeroFattura($db)) {

            /**
             * Genero il documento
             */
            $fatturaCliente = $this->intestazione($fatturaCliente);
            $fatturaCliente = $this->sezionePagamento($fatturaCliente, $fattura);
            $fatturaCliente = $this->sezioneBanca($fatturaCliente, $fattura);
            $fatturaCliente = $this->sezioneDestinatario($fatturaCliente, $cliente, $fattura);
            $fatturaCliente = $this->sezioneIdentificativiFattura($fatturaCliente, $fattura);

            if ($fattura->getTipFattura() == self::CONTRIBUTO) {
                $fatturaCliente = $this->sezioneNotaTesta($fatturaCliente, $fattura);
                $fatturaCliente = $this->sezioneDettagliFatturaContributo($fatturaCliente, $fattura, $dettaglioFattura, 15, 180);
                $fatturaCliente = $this->sezioneNotaPiede($fatturaCliente, $fattura);
                $fatturaCliente = $this->sezioneTotaliContributo($fatturaCliente, $fattura);
            } else {
                $fatturaCliente = $this->sezioneDettagliFatturaVendita($fatturaCliente, $fattura, $dettaglioFattura);
                $fatturaCliente = $this->sezioneNotaPiede($fatturaCliente, $fattura);
                $fatturaCliente = $this->sezioneTotaliVendita($fatturaCliente, $fattura);
            }
            $fatturaCliente->Output();
        }

        $creaFatturaClienteTemplate = CreaFatturaClienteTemplate::getInstance();
        $this->preparaPagina($creaFatturaClienteTemplate);

        $replace = parent::getIndexSession(self::AMBIENTE) !== NULL ? array('%amb%' => parent::getIndexSession(self::AMBIENTE), '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array), '%menu%' => $this->makeMenu($utility));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);

        $creaFatturaClienteTemplate->displayPagina();
        include($this->piede);
    }

    private function sezioneIdentificativiFattura($documento, $fattura) {
        $documento->identificativiFatturaCliente($fattura->getGiorno(), $fattura->getMeserif(), $fattura->getAnno(), $fattura->getNumFattura(), $fattura->getCodNegozio());
        return $documento;
    }

    private function sezioneNotaTesta($documento, $fattura) {

        $utility = Utility::getInstance();

        if (parent::isNotEmpty($fattura->getNotaTesta())) {
            $nota = explode("\\", $fattura->getNotaTesta());

            $replace = array(
                '%ASSISTITO%' => trim($fattura->getAssistito())
            );

            /**
             * Cerco il placeholder negli spezzoni della nota
             */
            $i = 0;
            foreach ($nota as $spezzone) {
                $nota[$i] = $utility->tailFile($spezzone, $replace);
                $i++;
            }
        }
        $documento->aggiungiLineaNota($nota, 15, 120);
        return $documento;
    }

    private function sezioneDettagliFatturaVendita($documento, $fattura, $dettaglioFattura) {

        $r1 = 10;
        $r2 = $r1 + 192;
        $y1 = 118;
        $documento->SetDrawColor(204, 204, 204);

        $tot_imponibile_10 = 0;
        $tot_iva_10 = 0;
        $tot_imponibile_22 = 0;
        $tot_iva_22 = 0;
        $tot_imponibile = 0;
        $tot_iva = 0;

        $documento->boxDettagli();

        $w = array(10, 82, 25, 20, 25, 15, 15);
        $h = array("QTA'", "DESCRIZIONE", "PREZZO", "TOTALE", "IMPONIBILE", "IVA", "C.IVA");

        $documento->Line($r1, $y1, $r2, $y1);  // linea dopo intestazione

        /**
         * Linee colonne
         */
        $rc1 = 10;
        $yc1 = 240;
        $yc2 = 106;

        for ($i = 0; $i < count($w) - 1; $i++) {
            $rc1 += $w[$i];
            $documento->Line($rc1, $yc1, $rc1, $yc2);
        }

        /**
         * Intestazioni colonne
         */
        $documento->SetXY($r1, $y1 - 10);

        for ($i = 0; $i < count($h); $i++) {

            /**
             * Allineamento intestazioni
             */
            if ($h[$i] == "DESCRIZIONE")
                $align = "L";
            elseif ($h[$i] == "C.IVA")
                $align = "C";
            else
                $align = "R";

            $documento->Cell($w[$i], 7, $h[$i], "", 0, $align);
        }

        /**
         * Linee fattura
         */
        $documento->SetXY($r1, $y1);

        foreach ($dettaglioFattura->getDettagliFattura() as $ele) {

            $linea = array("QUANTITA" => $ele[DettaglioFattura::QTA_ARTICOLO],
                "ARTICOLO" => $ele[DettaglioFattura::DES_ARTICOLO],
                "IMPORTO U." => $ele[DettaglioFattura::IMP_ARTICOLO],
                "TOTALE" => $ele[DettaglioFattura::IMP_TOTALE],
                "IMPONIBILE" => $ele[DettaglioFattura::IMP_IMPONIBILE],
                "IVA" => $ele[DettaglioFattura::IMP_IVA],
                "%IVA" => $ele[DettaglioFattura::COD_ALIQUOTA]
            );

            $documento->aggiungiLineaTabella($w, $linea);

            /**
             * Accumulo totali per aliquota iva
             */
            if ($ele[DettaglioFattura::COD_ALIQUOTA] == "10") {
                $tot_imponibile_10 += $ele[DettaglioFattura::IMP_IMPONIBILE];
                $tot_iva_10 += $ele[DettaglioFattura::IMP_IVA];
            } elseif ($ele[DettaglioFattura::COD_ALIQUOTA] == "22") {
                $tot_imponibile_22 += $ele[DettaglioFattura::IMP_IMPONIBILE];
                $tot_iva_22 += $ele[DettaglioFattura::IMP_IVA];
            } elseif ($ele[DettaglioFattura::COD_ALIQUOTA] == "5") {
                $tot_imponibile += $ele[DettaglioFattura::IMP_IMPONIBILE];
                $tot_iva += $ele[DettaglioFattura::IMP_IVA];
            }
        }

        /**
         * Salvo in sessione i totali accumulati
         */
        $fattura->setTotImponibile($tot_imponibile);
        $fattura->setTotIva($tot_iva);
        $fattura->setTotImponibile10($tot_imponibile_10);
        $fattura->setTotIva10($tot_iva_10);
        $fattura->setTotImponibile22($tot_imponibile_22);
        $fattura->setTotIva22($tot_iva_22);

        parent::setIndexSession(self::FATTURA, serialize($fattura));

        /**
         * Closing line
         */
        $r1 = 10;
        $r2 = $r1 + 192;
        $y1 = 240;
        $documento->Line($r1, $y1, $r2, $y1);

        return $documento;
    }

    private function sezioneDettagliFatturaContributo($documento, $fattura, $dettalioFattura, $r1, $y1) {

        $documento->boxDettagli();

        $tot_imponibile = 0;
        $tot_iva = 0;
        $w = array(125, 30, 30);

        for ($i = 0; $i < count($h); $i++)
            $documento->Cell($w[$i], 7, $h[$i], 1, 0, 'C');

        $documento->Ln();

        foreach ($dettalioFattura->getDettagliFattura() as $ele) {

            $linea = array("QUANTITA" => $ele[DettaglioFattura::QTA_ARTICOLO],
                "ARTICOLO" => $ele[DettaglioFattura::DES_ARTICOLO],
                "IMPORTO U." => $ele[DettaglioFattura::IMP_ARTICOLO],
                "TOTALE" => $ele[DettaglioFattura::IMP_TOTALE],
                "IMPONIBILE" => $ele[DettaglioFattura::IMP_IMPONIBILE],
                "IVA" => $ele[DettaglioFattura::IMP_IVA],
                "ALIQUOTA" => $ele[DettaglioFattura::COD_ALIQUOTA]
            );

            $documento->aggiungiLineaLiberaCliente($w, $linea, $r1, $y1);

            $tot_dettagli += $ele[DettaglioFattura::IMP_TOTALE];
            $tot_imponibile += $ele[DettaglioFattura::IMP_IMPONIBILE];
            $tot_iva += $ele[DettaglioFattura::IMP_IVA];
            $aliquota_iva = $ele[DettaglioFattura::COD_ALIQUOTA];
        }

        $fattura->setTotDettagli($tot_dettagli);
        $fattura->setTotImponibile($tot_imponibile);
        $fattura->setTotIva($tot_iva);
        $fattura->setAliquotaIva($aliquota_iva);

        parent::setIndexSession(self::FATTURA, serialize($fattura));

        /**
         * Closing line
         */
        $r1 = 10;
        $r2 = $r1 + 192;
        $y1 = 240;
        $documento->Line($r1, $y1, $r2, $y1);

        return $documento;
    }

    public function sezioneTotaliContributo($documento, $fattura) {
        $documento->totaliFatturaContributoCliente($fattura->getTotDettagli(), $fattura->getTotImponibile(), $fattura->getTotIva(), $fattura->getAliquotaIva());
        return $documento;
    }

    public function sezioneTotaliVendita($documento, $fattura) {
        $documento->totaliFatturaVenditaCliente($fattura->getTotImponibile(), $fattura->getTotIva(), $fattura->getTotImponibile10(), $fattura->getTotIva10(), $fattura->getTotImponibile22(), $fattura->getTotIva22());
        return $documento;
    }

    public function preparaPagina() {

        parent::setIndexSession(self::TITOLO_PAGINA, "%ml.creaFatturaCliente%");

        $db = Database::getInstance();
        $utility = Utility::getInstance();

        // Prelievo dei clienti -------------------------------------------------------------

        parent::setIndexSession('elenco_clienti', $this->caricaClientiFatturabili($utility, $db, "1000")); // Categoria=1000 -> Cliente
    }

}

?>
<?php

require_once 'nexus6.abstract.class.php';
require_once 'fattura.presentation.interface.php';

abstract class FatturaAbstract extends Nexus6Abstract implements FatturaPresentationInterface {

    public static $messaggio;
    public static $queryRicercaClienti = "/fatture/ricercaClienti.sql";

    public function setMessaggio($messaggio) {
        self::$messaggio = $messaggio;
    }

    public function getMessaggio() {
        return self::$messaggio;
    }

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

        $sqlTemplate = $this->root . $array['query'] . self::$queryRicercaClienti;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        /**
         * Prepara un elenco da inserire in una array javascript adatta per un campo autocomplete
         */
        foreach (pg_fetch_all($result) as $row) {
            $elencoClienti .= '"' . $row["des_cliente"] . '",';
        }
        return $elencoClienti;
    }

    public function intestazione($documento) {

        $documento->setTitle(iconv('UTF-8', 'windows-1252', "Cooperativa Chopin - Cooperativa sociale - ONLUS"));
        $documento->setTitle1(iconv('UTF-8', 'windows-1252', "Diversamente Impresa: Esperienza occupazionale-lavorativa"));
        $documento->setTitle2(iconv('UTF-8', 'windows-1252', "Domicilio fiscale: via San Martirio, 1 - 24030 Villa d'Adda (BG) - C.F./P.IVA: 03691430163"));
        return $documento;
    }

    public function sezioneNotaPiede($documento, $fattura) {

        if (parent::isNotEmpty($fattura->getNotaPiede())) {
            $nota = explode("\\", $fattura->getNotaPiede());
        }
        $documento->aggiungiLineaNota($nota, 12, 242);
        return $documento;
    }

    public function sezionePagamento($documento, $fattura) {
        $documento->AddPage();
        $documento->pagamento($fattura->getTipAddebito());
        return $documento;
    }

    public function sezioneBanca($documento, $fattura) {
        $documento->banca($fattura->getDesRagsocBanca(), $fattura->getCodIbanBanca());
        return $documento;
    }

    public function sezioneDestinatario($documento, $cliente, $fattura) {
        $documento->destinatario($cliente->getDesCliente(), $cliente->getDesIndirizzoCliente(), $cliente->getDesCittaCliente(), $cliente->getCapCliente(), $cliente->getCodPiva(), $cliente->getCodFisc(), $fattura->getDesTitolo());
        return $documento;
    }

    public function makeTabellaDettagliFattura($dettaglioFattura) {

        $thead = "";
        $tbody = "";

        if ($dettaglioFattura->getQtaDettagliFattura() > 0) {

            $tbody = "<tbody>";
            $thead = "" .
                    "<thead>" .
                    "   <tr>" .
                    "       <th>Qta</th>" .
                    "       <th>Articolo</th>" .
                    "       <th>Importo</th>" .
                    "       <th>Aliquota</th>" .
                    "       <th>Totale</th>" .
                    "       <th>Imponibile</th>" .
                    "       <th>Iva</th>" .
                    "       <th>&nbsp;</th>" .
                    "	</tr>" .
                    "</thead>";

            foreach ($dettaglioFattura->getDettagliFattura() as $unDettaglio) {

                $cancella_parms = $unDettaglio[DettaglioFattura::ID_ARTICOLO];

                $bottoneCancella = self::CANCELLA_DETTAGLIO_FATTURA_HREF . $cancella_parms . self::CANCELLA_ICON;

                $tbody .= "" .
                        "<tr>" .
                        "   <td>" . $unDettaglio[DettaglioFattura::QTA_ARTICOLO] . "</td>" .
                        "   <td>" . $unDettaglio[DettaglioFattura::DES_ARTICOLO] . "</td>" .
                        "   <td>" . $unDettaglio[DettaglioFattura::IMP_ARTICOLO] . "</td>" .
                        "   <td>" . $unDettaglio[DettaglioFattura::COD_ALIQUOTA] . "</td>" .
                        "   <td>" . $unDettaglio[DettaglioFattura::IMP_TOTALE] . "</td>" .
                        "   <td>" . $unDettaglio[DettaglioFattura::IMP_IMPONIBILE] . "</td>" .
                        "   <td>" . $unDettaglio[DettaglioFattura::IMP_IVA] . "</td>" .
                        "   <td align='center'>" . $bottoneCancella . "</td>" .
                        "</tr>";
            }
            $tbody .= "</tbody>";
            return "<table id='dettagli' class='table table-bordered'>" . $thead . $tbody . "</table>";
        }
        return "";
    }

}

?>
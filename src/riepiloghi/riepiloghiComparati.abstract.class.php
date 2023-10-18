<?php

require_once 'riepiloghi.abstract.class.php';

abstract class RiepiloghiComparatiAbstract extends RiepiloghiAbstract implements MainNexus6Interface {

    /**
     * Questo metodo costruisce una tabella html dei costi comparati
     * @param unknown $riepilogo
     */
    public function makeTableCostiComparati($riepilogo) {

//        $sottocontiCostiVariabili = ($array['sottocontiCostiVariabili'] != "") ? explode(",", $array['sottocontiCostiVariabili']) : "";

        $risultato_costi = $this->intestazioneTabellaRiepiloghiComparati();

        $totaleCosti = 0;
        $desconto_break = "";

        $totaleConto_Vil = 0;
        $totale_Vil = 0;

        foreach ($riepilogo->getCostiComparati() as $row) {

            $totaleConto = trim($row['tot_conto']);
            $totaleCosti += $totaleConto;

            if (trim($row['cod_negozio']) == self::ERBA) {
                $totale_Vil += $totaleConto;
            }

            if (trim($row['des_conto']) != $desconto_break) {
                if ($desconto_break != "") {

                    $totVil = ($totaleConto_Vil != 0) ? number_format(floatval($totaleConto_Vil), 2, ',', '.') : "&ndash;&ndash;&ndash;";
                    $totale = $totaleConto_Vil;
                    $tot = ($totale != 0) ? number_format(floatval($totale), 2, ',', '.') : "&ndash;&ndash;&ndash;";

                    $risultato_costi .= "" .
                            "<tr>" .
                            "	<td>" . $desconto_break . "</td>" .
                            "	<td class='text-right'>" . $totVil . "</td>" .
                            "	<td class='bg-info text-right'>" . $tot . "</td>" .
                            "</tr>";

                    $totaleConto_Vil = 0;
                }
                $desconto_break = trim($row['des_conto']);
            }

            if (trim($row['cod_negozio']) == self::ERBA)
                $totaleConto_Vil += $totaleConto;
        }

        $totVil = ($totaleConto_Vil != 0) ? number_format(floatval($totaleConto_Vil), 2, ',', '.') : "&ndash;&ndash;&ndash;";
        $totale = $totaleConto_Vil;
        $tot = ($totale != 0) ? number_format(floatval($totale), 2, ',', '.') : "&ndash;&ndash;&ndash;";

        $risultato_costi .= "" .
                "<tr>" .
                "   <td>" . $desconto_break . "</td>" .
                "   <td class='text-right'>" . $totVil . "</td>" .
                "   <td class='bg-info text-right'>" . $tot . "</td>" .
                "</tr>";

        /**
         * Totale complessivo di colonna
         */
        $totVil = ($totale_Vil != 0) ? number_format(floatval($totale_Vil), 2, ',', '.') : "&ndash;&ndash;&ndash;";
        $totale = $totale_Vil;
        $tot = ($totale != 0) ? number_format(floatval($totale), 2, ',', '.') : "&ndash;&ndash;&ndash;";

        $risultato_costi .= "" .
                "<tr>" .
                "   <td>%ml.totale% %ml.costi%</td>" .
                "   <td class='bg-info text-right'>" . $totVil . "</td>" .
                "   <td class='bg-info text-right'>" . $tot . "</td>" .
                "</tr>";

        $risultato_costi .= "</tbody></table>";

        $riepilogo->setTableCostiComparati($risultato_costi);
        $riepilogo->setTotaleCostiVilla($totale_Vil);
        $riepilogo->setTotaleCosti($totale);

        parent::setIndexSession(self::RIEPILOGO, serialize($riepilogo));
    }

    /**
     * Questo metodo costruisce una tabella html dei ricavi comparati
     */
    public function makeTableRicaviComparati($riepilogo) {

        $risultato_ricavi = $this->intestazioneTabellaRiepiloghiComparati();

        $totaleRicavi = 0;
        $desconto_break = "";

        $totaleConto_Vil = 0;
        $totale_Vil = 0;

        foreach ($riepilogo->getRicaviComparati() as $row) {

            $totaleConto = trim($row['tot_conto']);
            $totaleRicavi += $totaleConto;

            if (trim($row['cod_negozio']) == self::ERBA) {
                $totale_Vil += $totaleConto;
            }

            if (trim($row['des_conto']) != $desconto_break) {
                if ($desconto_break != "") {

                    $totVil = ($totaleConto_Vil != 0) ? number_format(abs(floatval($totaleConto_Vil)), 2, ',', '.') : "&ndash;&ndash;&ndash;";
                    $totale = $totaleConto_Vil;
                    $tot = ($totale != 0) ? number_format(abs(floatval($totale)), 2, ',', '.') : "&ndash;&ndash;&ndash;";

                    $risultato_ricavi .= "" .
                            "<tr>" .
                            "	<td>" . $desconto_break . "</td>" .
                            "	<td class='text-right'>" . $totVil . "</td>" .
                            "	<td class='bg-info text-right'>" . $tot . "</td>" .
                            "</tr>";

                    $totaleConto_Vil = 0;
                }
                $desconto_break = trim($row['des_conto']);
            }

            if (trim($row['cod_negozio']) == self::ERBA) {
                $totaleConto_Vil += $totaleConto;
            }
        }

        $totVil = ($totaleConto_Vil != 0) ? number_format(abs(floatval($totaleConto_Vil)), 2, ',', '.') : "&ndash;&ndash;&ndash;";

        $totale = $totaleConto_Vil;
        $tot = ($totale != 0) ? number_format(abs(floatval($totale)), 2, ',', '.') : "&ndash;&ndash;&ndash;";

        $risultato_ricavi .= "" .
                "<tr>" .
                "   <td>" . $desconto_break . "</td>" .
                "   <td class='text-right'>" . $totVil . "</td>" .
                "   <td class='bg-info text-right'>" . $tot . "</td>" .
                "</tr>";

        /**
         * Totale complessivo di colonna
         */
        $totVil = ($totale_Vil != 0) ? number_format(abs(floatval($totale_Vil)), 2, ',', '.') : "&ndash;&ndash;&ndash;";

        $totale = $totale_Vil;
        $tot = ($totale != 0) ? number_format(abs(floatval($totale)), 2, ',', '.') : "&ndash;&ndash;&ndash;";

        $risultato_ricavi .= "" .
                "<tr>" .
                "   <td >%ml.totale% %ml.ricavi%</td>" .
                "   <td class='bg-info text-right'>" . $totVil . "</td>" .
                "   <td class='bg-info text-right'>" . $tot . "</td>" .
                "</tr>";

        $risultato_ricavi .= "</tbody></table>";

        $riepilogo->setTableRicaviComparati($risultato_ricavi);
        $riepilogo->setTotaleRicaviVilla(abs($totale_Vil));
        $riepilogo->setTotaleRicavi(abs($totale));

        parent::setIndexSession(self::RIEPILOGO, serialize($riepilogo));
    }

    /**
     * Questo metodo costruisce una tabella html delle attività comparate
     * @param unknown $array
     * @param unknown $dati
     */
    public function makeTableAttivoComparati($riepilogo) {

        /* @var $risultato_attivo string */
        $risultato_attivo = $this->intestazioneTabellaRiepiloghiComparati();

        $totaleAttivo = 0;
        $desconto_break = "";

        $totaleConto_Vil = 0;

        $totale_Vil = 0;

        foreach ($riepilogo->getAttivoComparati() as $row) {

            $totaleConto = trim($row['tot_conto']);
            $totaleAttivo += $totaleConto;

            if (trim($row['cod_negozio']) == self::ERBA) {
                $totale_Vil += $totaleConto;
            }

            if (trim($row['des_conto']) != $desconto_break) {

                if ($desconto_break != "") {

                    $totVil = ($totaleConto_Vil != 0) ? number_format(abs(floatval($totaleConto_Vil)), 2, ',', '.') : "&ndash;&ndash;&ndash;";
                    $totale = $totaleConto_Vil;
                    $tot = ($totale != 0) ? number_format(abs(floatval($totale)), 2, ',', '.') : "&ndash;&ndash;&ndash;";

                    $risultato_attivo .= "" .
                            "<tr>" .
                            "	<td>" . $desconto_break . "</td>" .
                            "	<td class='text-right'>" . $totVil . "</td>" .
                            "	<td class='bg-info text-right'>" . $tot . "</td>" .
                            "</tr>";

                    $totaleConto_Vil = 0;
                }
                $desconto_break = trim($row['des_conto']);
            }

            if (trim($row['cod_negozio']) == self::ERBA) {
                $totaleConto_Vil += $totaleConto;
            }
        }

        $totVil = ($totaleConto_Vil != 0) ? number_format(abs(floatval($totaleConto_Vil)), 2, ',', '.') : "&ndash;&ndash;&ndash;";
        $totale = $totaleConto_Vil;
        $tot = ($totale != 0) ? number_format(abs(floatval($totale)), 2, ',', '.') : "&ndash;&ndash;&ndash;";

        $risultato_attivo .= "" .
                "<tr>" .
                "   <td>" . $desconto_break . "</td>" .
                "   <td class='text-right'>" . $totVil . "</td>" .
                "   <td class='bg-info text-right'>" . $tot . "</td>" .
                "</tr>";

        /**
         * Totale complessivo di colonna
         */
        $totVil = ($totale_Vil != 0) ? number_format(abs(floatval($totale_Vil)), 2, ',', '.') : "&ndash;&ndash;&ndash;";
        $totale = $totale_Vil;
        $tot = ($totale != 0) ? number_format(abs(floatval($totale)), 2, ',', '.') : "&ndash;&ndash;&ndash;";

        $risultato_attivo .= "" .
                "<tr>" .
                "   <td>%ml.totale% %ml.attivo%</td>" .
                "   <td class='bg-info text-right'>" . $totVil . "</td>" .
                "   <td class='bg-info text-right'>" . $tot . "</td>" .
                "</tr>";

        $risultato_attivo .= "</tbody></table>";

        $riepilogo->setTableAttivoComparati($risultato_attivo);
        $riepilogo->setTotaleAttivoVilla(abs($totale_Vil));
        $riepilogo->setTotaleAttivo(abs($totale));

        parent::setIndexSession(self::RIEPILOGO, serialize($riepilogo));
    }

    /**
     * Questo metodo costruisce una tabella html delle passività comparate
     * @param unknown $array
     * @param unknown $dati
     */
    public function makeTablePassivoComparati($riepilogo) {

        $risultato_passivo = $this->intestazioneTabellaRiepiloghiComparati();

        $totalePassivo = 0;
        $desconto_break = "";

        $totaleConto_Vil = 0;
        $totale_Vil = 0;

        foreach ($riepilogo->getPassivoComparati() as $row) {

            $totaleConto = trim($row['tot_conto']);
            $totalePassivo += $totaleConto;

            if (trim($row['cod_negozio']) == self::ERBA) {
                $totale_Vil += $totaleConto;
            }

            if (trim($row['des_conto']) != $desconto_break) {

                if ($desconto_break != "") {

                    $totVil = ($totaleConto_Vil != 0) ? number_format(abs(floatval($totaleConto_Vil)), 2, ',', '.') : "&ndash;&ndash;&ndash;";
                    $totale = $totaleConto_Vil;
                    $tot = ($totale != 0) ? number_format(abs(floatval($totale)), 2, ',', '.') : "&ndash;&ndash;&ndash;";

                    $risultato_passivo .= "" .
                            "<tr>" .
                            "	<td>" . $desconto_break . "</td>" .
                            "	<td class='text-right'>" . $totVil . "</td>" .
                            "	<td class='bg-info text-right'>" . $tot . "</td>" .
                            "</tr>";

                    $totaleConto_Vil = 0;
                }
                $desconto_break = trim($row['des_conto']);
            }

            if (trim($row['cod_negozio']) == self::ERBA) {
                $totaleConto_Vil += $totaleConto;
            }
        }

        $totVil = ($totaleConto_Vil != 0) ? number_format(abs(floatval($totaleConto_Vil)), 2, ',', '.') : "&ndash;&ndash;&ndash;";
        $totale = $totaleConto_Vil;
        $tot = ($totale != 0) ? number_format(abs(floatval($totale)), 2, ',', '.') : "&ndash;&ndash;&ndash;";

        $risultato_passivo .= "" .
                "<tr>" .
                "   <td>" . $desconto_break . "</td>" .
                "   <td class='text-right'>" . $totVil . "</td>" .
                "   <td class='bg-info text-right'>" . $tot . "</td>" .
                "</tr>";

        /**
         * Totale complessivo di colonna
         */
        $totVil = ($totale_Vil != 0) ? number_format(abs(floatval($totale_Vil)), 2, ',', '.') : "&ndash;&ndash;&ndash;";
        $totale = $totale_Vil;
        $tot = ($totale != 0) ? number_format(abs(floatval($totale)), 2, ',', '.') : "&ndash;&ndash;&ndash;";

        $risultato_passivo .= "" .
                "<tr>" .
                "   <td>%ml.totale% %ml.passivo%</td>" .
                "   <td class='bg-info text-right'>" . $totVil . "</td>" .
                "   <td class='bg-info text-right'>" . $tot . "</td>" .
                "</tr>";

        $risultato_passivo .= "</tbody></table>";

        /**
         * Metto in sessione i totali attivi
         */
        $riepilogo->setTablePassivoComparati($risultato_passivo);
        $riepilogo->setTotalePassivoVilla(abs($totale_Vil));
        $riepilogo->setTotalePassivo(abs($totale));

        parent::setIndexSession(self::RIEPILOGO, serialize($riepilogo));
    }

    /**
     * Questo metodo calcola l'MCT per ciascun negozio
     * @return html table dei risultati
     */
    public function makeTableMct($riepilogo) {

        $margineContribuzione = "";
        $totaleCostiVariabili = 0;
        $totaleRicavi = 0;
        $totaleCostiFissi = 0;
        $margineTotale = 0;
        $marginePercentuale = 0;
        $ricaricoPercentuale = 0;

        // Villa ---------------------------------------------------------------------

        $totaleCostiVariabiliVIL = 0;
        $totaleRicaviVIL = 0;
        $totaleCostiFissiVIL = 0;
        $margineTotaleVIL = 0;
        $marginePercentualeVIL = 0;
        $ricaricoPercentualeVIL = 0;

        foreach ($riepilogo->getCostoVariabileVilla() as $row) {
            $totaleCostiVariabiliVIL = trim($row['totalecostovariabile']);
        }

        foreach ($riepilogo->getRicavoVenditaProdottiVilla() as $row) {
            $totaleRicaviVIL = trim($row['totalericavovendita']);
        }

        foreach ($riepilogo->getCostoFissoVilla() as $row) {
            $totaleCostiFissiVIL = trim($row['totalecostofisso']);
        }

        $margineTotaleVIL = abs($totaleRicaviVIL) - $totaleCostiVariabiliVIL;
        if ($totaleRicaviVIL > parent::ZERO_VALUE) {
            $marginePercentualeVIL = ($margineTotaleVIL * 100 ) / abs($totaleRicaviVIL);
        }
        if ($totaleCostiVariabiliVIL > parent::ZERO_VALUE) {
            $ricaricoPercentualeVIL = ($margineTotaleVIL * 100) / abs($totaleCostiVariabiliVIL);
        }

        $totaleRicavi += abs($totaleRicaviVIL);
        $totaleCostiVariabili += $totaleCostiVariabiliVIL;

        // MCT totale negozi ---------------------------------------------------------------------

        $margineTotale = abs($totaleRicavi) - $totaleCostiVariabili;
        $marginePercentuale = ($margineTotale * 100 ) / abs($totaleRicavi);
        $ricaricoPercentuale = ($margineTotale * 100) / abs($totaleCostiVariabili);

        /**
         * Creo la tabella
         */
        $margineContribuzione = $this->intestazioneTabellaRiepiloghiComparati();

        $margineContribuzione .= "" .
                "<tr>" .
                "   <td>%ml.fatturato%</td>" .
                "   <td class='text-right'>&euro; " . number_format(abs(floatval($totaleRicaviVIL)), 2, ',', '.') . "</td>" .
                "   <td class='bg-info text-right'>&euro; " . number_format(abs(floatval($totaleRicavi)), 2, ',', '.') . "</td>" .
                "</tr>" .
                "<tr>" .
                "   <td>%ml.acquisti%</td>" .
                "   <td class='text-right'>&euro; " . number_format(abs(floatval($totaleCostiVariabiliVIL)), 2, ',', '.') . "</td>" .
                "   <td class='bg-info text-right'>&euro; " . number_format(abs(floatval($totaleCostiVariabili)), 2, ',', '.') . "</td>" .
                "</tr>" .
                "<tr>" .
                "   <td>%ml.margineAssoluto%</td>" .
                "   <td class='text-right'>&euro; " . number_format(floatval($margineTotaleVIL), 2, ',', '.') . "</td>" .
                "   <td class='bg-info text-right'>&euro; " . number_format(floatval($margineTotale), 2, ',', '.') . "</td>" .
                "</tr>" .
                "<tr>" .
                "   <td>%ml.marginePercentuale%</td>" .
                "   <td class='text-right'>" . number_format(floatval($marginePercentualeVIL), 2, ',', '.') . " &#37;</td>" .
                "   <td class='bg-info text-right'>" . number_format(floatval($marginePercentuale), 2, ',', '.') . " &#37;</td>" .
                "</tr>" .
                "<tr>" .
                "   <td>%ml.ricaricoPercentuale%</td>" .
                "   <td class='text-right'>" . number_format(floatval($ricaricoPercentualeVIL), 2, ',', '.') . " &#37;</td>" .
                "   <td class='bg-info text-right'>" . number_format(floatval($ricaricoPercentuale), 2, ',', '.') . " &#37;</td>" .
                "</tr>" .
                "</tbody>" .
                "</table>";

        $riepilogo->setTableMctComparati($margineContribuzione);

        parent::setIndexSession(self::RIEPILOGO, serialize($riepilogo));
    }

    /**
     * Questo metodo costruisce una tabella html per i risultati del calcolo del BEP
     */
    public function makeTableBep($riepilogo) {

        /**
         * Calcolo del Break Eaven Point
         *
         * Il calcolo del BEP per un’azienda che realizza prodotti
         * si ottiene imponendo l’eguaglianza fra il fatturato totale e i costi totali ovvero
         *
         *             Fatturato totale = Costi totali
         *
         * Metodo analitico: scrivendo le formule1 che esprimono i costi totali ed i ricavi,
         * con qualche passaggio matematico è possibile determinare che si intersecano se:
         *
         *              BEP = CF / (1 – (CV / FAT))
         *
         * Dove:
         *
         * FAT è il fatturato
         * CF sono i costi fissi
         * CV sono i costi variabili e quindi CV/FAT è l’incidenza dei costi variabili sul fatturato
         * CT sono i costi totali e quindi CT = CF + CV
         *
         */
        $tabellaBep = "";

        $totaleCostiVariabili = 0;
        $totaleRicavi = 0;
        $totaleCostiFissi = 0;
        $incidenzaCostiVariabiliSulFatturato = 0;
        $bep = 0;

        // Villa ---------------------------------------------------------------------

        $totaleCostiVariabiliVIL = 0;
        $totaleRicaviVIL = 0;
        $totaleCostiFissiVIL = 0;
        $incidenzaCostiVariabiliSulFatturatoVIL = 0;
        $bepVIL = 0;

        foreach ($riepilogo->getCostoVariabileVilla() as $row) {
            $totaleCostiVariabiliVIL = trim($row['totalecostovariabile']);
        }

        foreach ($riepilogo->getRicavoVenditaProdottiVilla() as $row) {
            $totaleRicaviVIL = trim($row['totalericavovendita']);
        }

        foreach ($riepilogo->getCostoFissoVilla() as $row) {
            $totaleCostiFissiVIL = trim($row['totalecostofisso']);
        }

        if ($totaleRicaviVIL > 0) {
            $incidenzaCostiVariabiliSulFatturatoVIL = 1 - ($totaleCostiVariabiliVIL / abs($totaleRicaviVIL));
            $bepVIL = $totaleCostiFissiVIL / round($incidenzaCostiVariabiliSulFatturatoVIL, 2);
        }

        $totaleCostiVariabili += $totaleCostiVariabiliVIL;
        $totaleRicavi += $totaleRicaviVIL;
        $totaleCostiFissi += $totaleCostiFissiVIL;

        // BEP totale negozi -----------------------------------------------------

        if ($totaleRicavi > parent::ZERO_VALUE) {
            $incidenzaCostiVariabiliSulFatturato = 1 - ($totaleCostiVariabili / abs($totaleRicavi));
            $bep = $totaleCostiFissi / round($incidenzaCostiVariabiliSulFatturato, 2);
        }

        /**
         * tabella del BEP
         */
        $tabellaBep = $this->intestazioneTabellaRiepiloghiComparati();

        $tabellaBep .= "" .
                "<tr>" .
                "   <td>Fatturato</td>" .
                "   <td class='text-right'>&euro; " . number_format(abs(floatval($totaleRicaviVIL)), 2, ',', '.') . "</td>" .
                "   <td class='bg-info text-right'>&euro; " . number_format(abs(floatval($totaleRicavi)), 2, ',', '.') . "</td>" .
                "</tr>" .
                "<tr>" .
                "   <td>Costi fissi</td>" .
                "   <td class='text-right'>&euro; " . number_format(abs(floatval($totaleCostiFissiVIL)), 2, ',', '.') . "</td>" .
                "   <td class='bg-info text-right'>&euro; " . number_format(abs(floatval($totaleCostiFissi)), 2, ',', '.') . "</td>" .
                "</tr>" .
                "<tr>" .
                "   <td>Acquisti</td>" .
                "   <td class='text-right'>&euro; " . number_format(abs(floatval($totaleCostiVariabiliVIL)), 2, ',', '.') . "</td>" .
                "   <td class='bg-info text-right'>&euro; " . number_format(abs(floatval($totaleCostiVariabili)), 2, ',', '.') . "</td>" .
                "</tr>" .
                "<tr>" .
                "   <td>Incidenza acquisti sul fatturato</td>" .
                "   <td class='text-right'> " . number_format(floatval($incidenzaCostiVariabiliSulFatturatoVIL), 2, ',', '.') . "</td>" .
                "   <td class='bg-info text-right'> " . number_format(floatval($incidenzaCostiVariabiliSulFatturato), 2, ',', '.') . "</td>" .
                "</tr>" .
                "<tr>" .
                "   <td>BEP</td>" .
                "   <td class='text-right'>&euro; " . number_format(floatval($bepVIL), 2, ',', '.') . "</td>" .
                "   <td class='bg-info text-right'>&euro; " . number_format(floatval($bep), 2, ',', '.') . "</td>" .
                "</tr>" .
                "</tbody></table>";


        $riepilogo->setTableBepComparati($tabellaBep);

        parent::setIndexSession(self::RIEPILOGO, serialize($riepilogo));
    }

    public function makeTabs($riepilogo) {

        $tabs = self::EMPTYSTRING;

        if ((parent::isNotEmpty($riepilogo->getTableCostiComparati()) or ( parent::isNotEmpty($riepilogo->getTableRicaviComparati())) or ( parent::isNotEmpty($riepilogo->getTableAttivoComparati())) or ( parent::isNotEmpty($riepilogo->getTablePassivoComparati())))) {

            $tabs = "<ul class='nav nav-tabs' role='tablist'>";

            if (parent::isNotEmpty($riepilogo->getTableCostiComparati())) {
                $tabs .= "<li role='presentation' class='active'><a href='#tabs-1' aria-controls='Costi' role='tab' data-toggle='tab'>Costi</a></li>";
            }
            if (parent::isNotEmpty($riepilogo->getTableRicaviComparati())) {
                $tabs .= "<li role='presentation'><a href='#tabs-2' aria-controls='Ricavi' role='tab' data-toggle='tab'>Ricavi</a></li>";
            }
            if (parent::isNotEmpty($riepilogo->getTableAttivoComparati())) {
                $tabs .= "<li role='presentation'><a href='#tabs-3' aria-controls='Attivo' role='tab' data-toggle='tab'>Attivo</a></li>";
            }
            if (parent::isNotEmpty($riepilogo->getTablePassivoComparati())) {
                $tabs .= "<li role='presentation'><a href='#tabs-4' aria-controls='Passivo' role='tab' data-toggle='tab'>Passivo</a></li>";
            }

            $tabs .= "<li role='presentation'><a href='#tabs-5' aria-controls='xxxx' role='tab' data-toggle='tab'>" . strtoupper($this->nomeTabTotali(abs($riepilogo->getTotaleRicavi()), abs($riepilogo->getTotaleCosti()))) . "</a></li>";

            if (parent::isNotEmpty($riepilogo->getTableMctComparati())) {
                $tabs .= "<li role='presentation'><a href='#tabs-6' aria-controls='MCT' role='tab' data-toggle='tab'>MCT</a></li>";
            }
            if (parent::isNotEmpty($riepilogo->getTableBepComparati())) {
                $tabs .= "<li role='presentation'><a href='#tabs-7' aria-controls='BEP' role='tab' data-toggle='tab'>BEP</a></li>";
            }
            $tabs .= "</ul>";

            $tabs .= "<div class='tab-content'>";

            if (parent::isNotEmpty($riepilogo->getTableCostiComparati())) {
                $tabs .= "<div role='tabpanel' class='tab-pane fade in active' id='tabs-1'>" . $riepilogo->getTableCostiComparati() . "</div>";
            }
            if (parent::isNotEmpty($riepilogo->getTableRicaviComparati())) {
                $tabs .= "<div role='tabpanel' class='tab-pane fade' id='tabs-2'>" . $riepilogo->getTableRicaviComparati() . "</div>";
            }
            if (parent::isNotEmpty($riepilogo->getTableAttivoComparati())) {
                $tabs .= "<div role='tabpanel' class='tab-pane fade' id='tabs-3'>" . $riepilogo->getTableAttivoComparati() . "</div>";
            }
            if (parent::isNotEmpty($riepilogo->getTablePassivoComparati())) {
                $tabs .= "<div role='tabpanel' class='tab-pane fade' id='tabs-4'>" . $riepilogo->getTablePassivoComparati() . "</div>";
            }

            $tabs .= "<div role='tabpanel' class='tab-pane fade' id='tabs-5'>" . $this->tabellaTotaliRiepilogoNegozi($riepilogo, $this->nomeTabTotali(abs($riepilogo->getTotaleRicavi()), abs($riepilogo->getTotaleCosti()))) . "</div>";

            if (parent::isNotEmpty($riepilogo->getTableMctComparati())) {
                $tabs .= "<div role='tabpanel' class='tab-pane fade' id='tabs-6'>" . $riepilogo->getTableMctComparati() . "</div>";
            }
            if (parent::isNotEmpty($riepilogo->getTableBepComparati())) {
                $tabs .= "<div role='tabpanel' class='tab-pane fade' id='tabs-7'>" . $riepilogo->getTableBepComparati() . "</div>";
            }

            $tabs .= "</div>";
        }
        return $tabs;
    }

    public function tabellaTotaliRiepilogoNegozi($riepilogo, $tipoTotale) {

        if ($tipoTotale == self::UTILE) {

            $risultato_esercizio = $this->intestazioneTabellaRiepiloghiComparati();

            $risultato_esercizio .= "" .
                    "<tr>" .
                    "	<td>Totale Ricavi</td>" .
                    "	<td class='text-right'>" . number_format(floatval($riepilogo->getTotaleRicaviVilla()), 2, ',', '.') . "</td>" .
                    "	<td class='bg-info text-right'>" . number_format(floatval($riepilogo->getTotaleRicavi()), 2, ',', '.') . "</td>" .
                    "</tr>" .
                    "<tr>" .
                    "	<td>Totale Costi</td>" .
                    "	<td class='text-right'>" . number_format(floatval($riepilogo->getTotaleCostiVilla()), 2, ',', '.') . "</td>" .
                    "	<td class='bg-info text-right'>" . number_format(floatval($riepilogo->getTotaleCosti()), 2, ',', '.') . "</td>" .
                    "</tr>";

            $utile_Vil = $riepilogo->getTotaleRicaviVilla() - $riepilogo->getTotaleCostiVilla();
            $utile = $utile_Vil;

            $risultato_esercizio .= "" .
                    "<tr>" .
                    "	<td>Utile del Periodo</td>" .
                    "	<td class='text-right'>" . number_format(floatval($utile_Vil), 2, ',', '.') . "</td>" .
                    "	<td class='bg-info text-right'>" . number_format(floatval($utile), 2, ',', '.') . "</td>" .
                    "</tr>" .
                    "</tbody></table>";
        } elseif ($tipoTotale == self::PERDITA) {

            $risultato_esercizio = $this->intestazioneTabellaRiepiloghiComparati();

            $risultato_esercizio .= "" .
                    "<tr>" .
                    "	<td>Totale Ricavi</td>" .
                    "	<td class='text-right'>" . number_format(floatval($riepilogo->getTotaleRicaviVilla()), 2, ',', '.') . "</td>" .
                    "	<td class='bg-info text-right'>" . number_format(floatval($riepilogo->getTotaleRicavi()), 2, ',', '.') . "</td>" .
                    "</tr>" .
                    "<tr>" .
                    "	<td>Totale Costi</td>" .
                    "	<td class='text-right'>" . number_format(floatval($riepilogo->getTotaleCostiVilla()), 2, ',', '.') . "</td>" .
                    "	<td class='bg-info text-right'>" . number_format(floatval($riepilogo->getTotaleCosti()), 2, ',', '.') . "</td>" .
                    "</tr>";

            $perdita_Vil = $riepilogo->getTotaleRicaviVilla() - $riepilogo->getTotaleCostiVilla();
            $perdita = $perdita_Vil;

            $risultato_esercizio .= "" .
                    "<tr>" .
                    "	<td>Perdita del Periodo</td>" .
                    "	<td class='text-right'>" . number_format(floatval($perdita_Vil), 2, ',', '.') . "</td>" .
                    "	<td class='bg-info text-right'>" . number_format(floatval($perdita), 2, ',', '.') . "</td>" .
                    "</tr>" .
                    "</tbody></table>";
        } else {

            $risultato_esercizio = "<table class='table table-bordered table-hover'>";

            $risultato_esercizio .= "<tr height='30'>" .
                    "	<td width='308' align='left' class='mark'>Totale Ricavi</td>" .
                    "	<td width='108' align='right' class='mark'>" . number_format(abs(floatval($riepilogo->getTotaleRicavi())), 2, ',', '.') . "</td>" .
                    "</tr>";

            $risultato_esercizio .= "<tr height='30'>" .
                    "	<td width='308' align='left' class='mark'>Totale Costi</td>" .
                    "	<td width='108' align='right' class='mark'>" . number_format(abs(floatval($riepilogo->getTotaleCosti())), 2, ',', '.') . "</td>" .
                    "</tr>";

            $pareggio = $riepilogo->getTotaleRicavi() - $riepilogo->getTotaleCosti();

            $risultato_esercizio .= "<tr height='30'>" .
                    "	<td width='308' align='left' class='mark'>Utile del Periodo</td>" .
                    "	<td width='108' align='right' class='mark'>" . number_format(floatval($pareggio), 2, ',', '.') . "</td>" .
                    "</tr>";

            $risultato_esercizio .= "</tbody></table>";
        }
        return $risultato_esercizio;
    }
}

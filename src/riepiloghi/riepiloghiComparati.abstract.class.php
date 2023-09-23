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

        $totaleConto_Bre = 0;
        $totaleConto_Tre = 0;
        $totaleConto_Vil = 0;

        $totale_Bre = 0;
        $totale_Tre = 0;
        $totale_Vil = 0;

        foreach ($riepilogo->getCostiComparati() as $row) {

            $totaleConto = trim($row['tot_conto']);
            $totaleCosti += $totaleConto;

            if (trim($row['cod_negozio']) == self::ERBA)
                $totale_Vil += $totaleConto;

            if (trim($row['des_conto']) != $desconto_break) {

                if ($desconto_break != "") {

                    $totBre = ($totaleConto_Bre != 0) ? number_format(floatval($totaleConto_Bre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
                    $totTre = ($totaleConto_Tre != 0) ? number_format(floatval($totaleConto_Tre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
                    $totVil = ($totaleConto_Vil != 0) ? number_format(floatval($totaleConto_Vil), 2, ',', '.') : "&ndash;&ndash;&ndash;";

                    $totale = $totaleConto_Bre + $totaleConto_Tre + $totaleConto_Vil;
                    $tot = ($totale != 0) ? number_format(floatval($totale), 2, ',', '.') : "&ndash;&ndash;&ndash;";

                    $risultato_costi .= "" .
                            "<tr>" .
                            "	<td>" . $desconto_break . "</td>" .
                            "	<td class='text-right'>" . $totBre . "</td>" .
                            "	<td class='text-right'>" . $totTre . "</td>" .
                            "	<td class='text-right'>" . $totVil . "</td>" .
                            "	<td class='bg-info text-right'>" . $tot . "</td>" .
                            "</tr>";

                    $totaleConto_Bre = 0;
                    $totaleConto_Tre = 0;
                    $totaleConto_Vil = 0;
                }

                $desconto_break = trim($row['des_conto']);
            }

            if (trim($row['cod_negozio']) == self::ERBA)
                $totaleConto_Vil += $totaleConto;
        }

        $totBre = ($totaleConto_Bre != 0) ? number_format(floatval($totaleConto_Bre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
        $totTre = ($totaleConto_Tre != 0) ? number_format(floatval($totaleConto_Tre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
        $totVil = ($totaleConto_Vil != 0) ? number_format(floatval($totaleConto_Vil), 2, ',', '.') : "&ndash;&ndash;&ndash;";

        $totale = $totaleConto_Bre + $totaleConto_Tre + $totaleConto_Vil;
        $tot = ($totale != 0) ? number_format(floatval($totale), 2, ',', '.') : "&ndash;&ndash;&ndash;";

        $risultato_costi .= "" .
                "<tr>" .
                "   <td>" . $desconto_break . "</td>" .
                "   <td class='text-right'>" . $totBre . "</td>" .
                "   <td class='text-right'>" . $totTre . "</td>" .
                "   <td class='text-right'>" . $totVil . "</td>" .
                "   <td class='bg-info text-right'>" . $tot . "</td>" .
                "</tr>";

        /**
         * Totale complessivo di colonna
         */
        $totBre = ($totale_Bre != 0) ? number_format(floatval($totale_Bre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
        $totTre = ($totale_Tre != 0) ? number_format(floatval($totale_Tre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
        $totVil = ($totale_Vil != 0) ? number_format(floatval($totale_Vil), 2, ',', '.') : "&ndash;&ndash;&ndash;";

        $totale = $totale_Bre + $totale_Tre + $totale_Vil;
        $tot = ($totale != 0) ? number_format(floatval($totale), 2, ',', '.') : "&ndash;&ndash;&ndash;";

        $risultato_costi .= "" .
                "<tr>" .
                "   <td>%ml.totale% %ml.costi%</td>" .
                "   <td class='bg-info text-right'>" . $totBre . "</td>" .
                "   <td class='bg-info text-right'>" . $totTre . "</td>" .
                "   <td class='bg-info text-right'>" . $totVil . "</td>" .
                "   <td class='bg-info text-right'>" . $tot . "</td>" .
                "</tr>";

        $risultato_costi .= "</tbody></table>";

        $riepilogo->setTableCostiComparati($risultato_costi);
        $riepilogo->setTotaleCostiBrembate($totale_Bre);
        $riepilogo->setTotaleCostiTrezzo($totale_Tre);
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

        $totaleConto_Bre = 0;
        $totaleConto_Tre = 0;
        $totaleConto_Vil = 0;

        $totale_Bre = 0;
        $totale_Tre = 0;
        $totale_Vil = 0;

        foreach ($riepilogo->getRicaviComparati() as $row) {

            $totaleConto = trim($row['tot_conto']);
            $totaleRicavi += $totaleConto;

            if (trim($row['cod_negozio']) == self::ERBA)
                $totale_Vil += $totaleConto;

            if (trim($row['des_conto']) != $desconto_break) {

                if ($desconto_break != "") {

                    $totBre = ($totaleConto_Bre != 0) ? number_format(abs(floatval($totaleConto_Bre)), 2, ',', '.') : "&ndash;&ndash;&ndash;";
                    $totTre = ($totaleConto_Tre != 0) ? number_format(abs(floatval($totaleConto_Tre)), 2, ',', '.') : "&ndash;&ndash;&ndash;";
                    $totVil = ($totaleConto_Vil != 0) ? number_format(abs(floatval($totaleConto_Vil)), 2, ',', '.') : "&ndash;&ndash;&ndash;";

                    $totale = $totaleConto_Bre + $totaleConto_Tre + $totaleConto_Vil;
                    $tot = ($totale != 0) ? number_format(abs(floatval($totale)), 2, ',', '.') : "&ndash;&ndash;&ndash;";

                    $risultato_ricavi .= "" .
                            "<tr>" .
                            "	<td>" . $desconto_break . "</td>" .
                            "	<td class='text-right'>" . $totBre . "</td>" .
                            "	<td class='text-right'>" . $totTre . "</td>" .
                            "	<td class='text-right'>" . $totVil . "</td>" .
                            "	<td class='bg-info text-right'>" . $tot . "</td>" .
                            "</tr>";

                    $totaleConto_Bre = 0;
                    $totaleConto_Tre = 0;
                    $totaleConto_Vil = 0;
                }

                $desconto_break = trim($row['des_conto']);
            }

            if (trim($row['cod_negozio']) == self::ERBA)
                $totaleConto_Vil += $totaleConto;
        }

        $totBre = ($totaleConto_Bre != 0) ? number_format(abs(floatval($totaleConto_Bre)), 2, ',', '.') : "&ndash;&ndash;&ndash;";
        $totTre = ($totaleConto_Tre != 0) ? number_format(abs(floatval($totaleConto_Tre)), 2, ',', '.') : "&ndash;&ndash;&ndash;";
        $totVil = ($totaleConto_Vil != 0) ? number_format(abs(floatval($totaleConto_Vil)), 2, ',', '.') : "&ndash;&ndash;&ndash;";

        $totale = $totaleConto_Bre + $totaleConto_Tre + $totaleConto_Vil;
        $tot = ($totale != 0) ? number_format(abs(floatval($totale)), 2, ',', '.') : "&ndash;&ndash;&ndash;";

        $risultato_ricavi .= "" .
                "<tr>" .
                "   <td>" . $desconto_break . "</td>" .
                "   <td class='text-right'>" . $totBre . "</td>" .
                "   <td class='text-right'>" . $totTre . "</td>" .
                "   <td class='text-right'>" . $totVil . "</td>" .
                "   <td class='bg-info text-right'>" . $tot . "</td>" .
                "</tr>";

        /**
         * Totale complessivo di colonna
         */
        $totBre = ($totale_Bre != 0) ? number_format(abs(floatval($totale_Bre)), 2, ',', '.') : "&ndash;&ndash;&ndash;";
        $totTre = ($totale_Tre != 0) ? number_format(abs(floatval($totale_Tre)), 2, ',', '.') : "&ndash;&ndash;&ndash;";
        $totVil = ($totale_Vil != 0) ? number_format(abs(floatval($totale_Vil)), 2, ',', '.') : "&ndash;&ndash;&ndash;";

        $totale = $totale_Bre + $totale_Tre + $totale_Vil;
        $tot = ($totale != 0) ? number_format(abs(floatval($totale)), 2, ',', '.') : "&ndash;&ndash;&ndash;";

        $risultato_ricavi .= "" .
                "<tr>" .
                "   <td >%ml.totale% %ml.ricavi%</td>" .
                "   <td class='bg-info text-right'>" . $totBre . "</td>" .
                "   <td class='bg-info text-right'>" . $totTre . "</td>" .
                "   <td class='bg-info text-right'>" . $totVil . "</td>" .
                "   <td class='bg-info text-right'>" . $tot . "</td>" .
                "</tr>";

        $risultato_ricavi .= "</tbody></table>";

        $riepilogo->setTableRicaviComparati($risultato_ricavi);
        $riepilogo->setTotaleRicaviBrembate(abs($totale_Bre));
        $riepilogo->setTotaleRicaviTrezzo(abs($totale_Tre));
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

        $totaleConto_Bre = 0;
        $totaleConto_Tre = 0;
        $totaleConto_Vil = 0;

        $totale_Bre = 0;
        $totale_Tre = 0;
        $totale_Vil = 0;

        foreach ($riepilogo->getAttivoComparati() as $row) {

            $totaleConto = trim($row['tot_conto']);
            $totaleAttivo += $totaleConto;

            if (trim($row['cod_negozio']) == self::ERBA)
                $totale_Vil += $totaleConto;

            if (trim($row['des_conto']) != $desconto_break) {

                if ($desconto_break != "") {

                    $totBre = ($totaleConto_Bre != 0) ? number_format(abs(floatval($totaleConto_Bre)), 2, ',', '.') : "&ndash;&ndash;&ndash;";
                    $totTre = ($totaleConto_Tre != 0) ? number_format(abs(floatval($totaleConto_Tre)), 2, ',', '.') : "&ndash;&ndash;&ndash;";
                    $totVil = ($totaleConto_Vil != 0) ? number_format(abs(floatval($totaleConto_Vil)), 2, ',', '.') : "&ndash;&ndash;&ndash;";

                    $totale = $totaleConto_Bre + $totaleConto_Tre + $totaleConto_Vil;
                    $tot = ($totale != 0) ? number_format(abs(floatval($totale)), 2, ',', '.') : "&ndash;&ndash;&ndash;";

                    $risultato_attivo .= "" .
                            "<tr>" .
                            "	<td>" . $desconto_break . "</td>" .
                            "	<td class='text-right'>" . $totBre . "</td>" .
                            "	<td class='text-right'>" . $totTre . "</td>" .
                            "	<td class='text-right'>" . $totVil . "</td>" .
                            "	<td class='bg-info text-right'>" . $tot . "</td>" .
                            "</tr>";

                    $totaleConto_Bre = 0;
                    $totaleConto_Tre = 0;
                    $totaleConto_Vil = 0;
                }

                $desconto_break = trim($row['des_conto']);
            }

            if (trim($row['cod_negozio']) == self::ERBA)
                $totaleConto_Vil += $totaleConto;
        }

        /* @var $totBre int */
        $totBre = ($totaleConto_Bre != 0) ? number_format(abs(floatval($totaleConto_Bre)), 2, ',', '.') : "&ndash;&ndash;&ndash;";
        /* @var $totTre int */
        $totTre = ($totaleConto_Tre != 0) ? number_format(abs(floatval($totaleConto_Tre)), 2, ',', '.') : "&ndash;&ndash;&ndash;";
        /* @var $totVil int */
        $totVil = ($totaleConto_Vil != 0) ? number_format(abs(floatval($totaleConto_Vil)), 2, ',', '.') : "&ndash;&ndash;&ndash;";

        /* @var $totale int */
        $totale = $totaleConto_Bre + $totaleConto_Tre + $totaleConto_Vil;
        $tot = ($totale != 0) ? number_format(abs(floatval($totale)), 2, ',', '.') : "&ndash;&ndash;&ndash;";

        $risultato_attivo .= "" .
                "<tr>" .
                "   <td>" . $desconto_break . "</td>" .
                "   <td class='text-right'>" . $totBre . "</td>" .
                "   <td class='text-right'>" . $totTre . "</td>" .
                "   <td class='text-right'>" . $totVil . "</td>" .
                "   <td class='bg-info text-right'>" . $tot . "</td>" .
                "</tr>";

        /**
         * Totale complessivo di colonna
         */
        $totBre = ($totale_Bre != 0) ? number_format(abs(floatval($totale_Bre)), 2, ',', '.') : "&ndash;&ndash;&ndash;";
        $totTre = ($totale_Tre != 0) ? number_format(abs(floatval($totale_Tre)), 2, ',', '.') : "&ndash;&ndash;&ndash;";
        $totVil = ($totale_Vil != 0) ? number_format(abs(floatval($totale_Vil)), 2, ',', '.') : "&ndash;&ndash;&ndash;";

        $totale = $totale_Bre + $totale_Tre + $totale_Vil;
        $tot = ($totale != 0) ? number_format(abs(floatval($totale)), 2, ',', '.') : "&ndash;&ndash;&ndash;";

        $risultato_attivo .= "" .
                "<tr>" .
                "   <td>%ml.totale% %ml.attivo%</td>" .
                "   <td class='bg-info text-right'>" . $totBre . "</td>" .
                "   <td class='bg-info text-right'>" . $totTre . "</td>" .
                "   <td class='bg-info text-right'>" . $totVil . "</td>" .
                "   <td class='bg-info text-right'>" . $tot . "</td>" .
                "</tr>";

        $risultato_attivo .= "</tbody></table>";

        $riepilogo->setTableAttivoComparati($risultato_attivo);
        $riepilogo->setTotaleAttivoBrembate(abs($totale_Bre));
        $riepilogo->setTotaleAttivoTrezzo(abs($totale_Tre));
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

        $totaleConto_Bre = 0;
        $totaleConto_Tre = 0;
        $totaleConto_Vil = 0;

        $totale_Bre = 0;
        $totale_Tre = 0;
        $totale_Vil = 0;

        foreach ($riepilogo->getPassivoComparati() as $row) {

            $totaleConto = trim($row['tot_conto']);
            $totalePassivo += $totaleConto;

            if (trim($row['cod_negozio']) == self::ERBA)
                $totale_Vil += $totaleConto;

            if (trim($row['des_conto']) != $desconto_break) {

                if ($desconto_break != "") {

                    $totBre = ($totaleConto_Bre != 0) ? number_format(abs(floatval($totaleConto_Bre)), 2, ',', '.') : "&ndash;&ndash;&ndash;";
                    $totTre = ($totaleConto_Tre != 0) ? number_format(abs(floatval($totaleConto_Tre)), 2, ',', '.') : "&ndash;&ndash;&ndash;";
                    $totVil = ($totaleConto_Vil != 0) ? number_format(abs(floatval($totaleConto_Vil)), 2, ',', '.') : "&ndash;&ndash;&ndash;";

                    $totale = $totaleConto_Bre + $totaleConto_Tre + $totaleConto_Vil;
                    $tot = ($totale != 0) ? number_format(abs(floatval($totale)), 2, ',', '.') : "&ndash;&ndash;&ndash;";

                    $risultato_passivo .= "" .
                            "<tr>" .
                            "	<td>" . $desconto_break . "</td>" .
                            "	<td class='text-right'>" . $totBre . "</td>" .
                            "	<td class='text-right'>" . $totTre . "</td>" .
                            "	<td class='text-right'>" . $totVil . "</td>" .
                            "	<td class='bg-info text-right'>" . $tot . "</td>" .
                            "</tr>";

                    $totaleConto_Bre = 0;
                    $totaleConto_Tre = 0;
                    $totaleConto_Vil = 0;
                }

                $desconto_break = trim($row['des_conto']);
            }

            if (trim($row['cod_negozio']) == self::ERBA)
                $totaleConto_Vil += $totaleConto;
        }

        $totBre = ($totaleConto_Bre != 0) ? number_format(abs(floatval($totaleConto_Bre)), 2, ',', '.') : "&ndash;&ndash;&ndash;";
        $totTre = ($totaleConto_Tre != 0) ? number_format(abs(floatval($totaleConto_Tre)), 2, ',', '.') : "&ndash;&ndash;&ndash;";
        $totVil = ($totaleConto_Vil != 0) ? number_format(abs(floatval($totaleConto_Vil)), 2, ',', '.') : "&ndash;&ndash;&ndash;";

        $totale = $totaleConto_Bre + $totaleConto_Tre + $totaleConto_Vil;
        $tot = ($totale != 0) ? number_format(abs(floatval($totale)), 2, ',', '.') : "&ndash;&ndash;&ndash;";

        $risultato_passivo .= "" .
                "<tr>" .
                "   <td>" . $desconto_break . "</td>" .
                "   <td class='text-right'>" . $totBre . "</td>" .
                "   <td class='text-right'>" . $totTre . "</td>" .
                "   <td class='text-right'>" . $totVil . "</td>" .
                "   <td class='bg-info text-right'>" . $tot . "</td>" .
                "</tr>";

        /**
         * Totale complessivo di colonna
         */
        $totBre = ($totale_Bre != 0) ? number_format(abs(floatval($totale_Bre)), 2, ',', '.') : "&ndash;&ndash;&ndash;";
        $totTre = ($totale_Tre != 0) ? number_format(abs(floatval($totale_Tre)), 2, ',', '.') : "&ndash;&ndash;&ndash;";
        $totVil = ($totale_Vil != 0) ? number_format(abs(floatval($totale_Vil)), 2, ',', '.') : "&ndash;&ndash;&ndash;";

        $totale = $totale_Bre + $totale_Tre + $totale_Vil;
        $tot = ($totale != 0) ? number_format(abs(floatval($totale)), 2, ',', '.') : "&ndash;&ndash;&ndash;";

        $risultato_passivo .= "" .
                "<tr>" .
                "   <td>%ml.totale% %ml.passivo%</td>" .
                "   <td class='bg-info text-right'>" . $totBre . "</td>" .
                "   <td class='bg-info text-right'>" . $totTre . "</td>" .
                "   <td class='bg-info text-right'>" . $totVil . "</td>" .
                "   <td class='bg-info text-right'>" . $tot . "</td>" .
                "</tr>";

        $risultato_passivo .= "</tbody></table>";

        /**
         * Metto in sessione i totali attivi
         */
        $riepilogo->setTablePassivoComparati($risultato_passivo);
        $riepilogo->setTotalePassivoBrembate(abs($totale_Bre));
        $riepilogo->setTotalePassivoTrezzo(abs($totale_Tre));
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

        // Trezzo ---------------------------------------------------------------------

        $totaleCostiVariabiliTRE = 0;
        $totaleRicaviTRE = 0;
        $totaleCostiFissiTRE = 0;
        $margineTotaleTRE = 0;
        $marginePercentualeTRE = 0;
        $ricaricoPercentualeTRE = 0;

        foreach ($riepilogo->getCostoVariabileTrezzo() as $row) {
            $totaleCostiVariabiliTRE = trim($row['totalecostovariabile']);
        }

        foreach ($riepilogo->getRicavoVenditaProdottiTrezzo() as $row) {
            $totaleRicaviTRE = trim($row['totalericavovendita']);
        }

        foreach ($riepilogo->getCostoFissoTrezzo() as $row) {
            $totaleCostiFissiTRE = trim($row['totalecostofisso']);
        }

        $margineTotaleTRE = abs($totaleRicaviTRE) - $totaleCostiVariabiliTRE;
        if ($totaleRicaviTRE > parent::ZERO_VALUE) {
            $marginePercentualeTRE = ($margineTotaleTRE * 100 ) / abs($totaleRicaviTRE);
        }
        if ($totaleCostiVariabiliTRE > parent::ZERO_VALUE)
            $ricaricoPercentualeTRE = ($margineTotaleTRE * 100) / abs($totaleCostiVariabiliTRE);

        $totaleRicavi += abs($totaleRicaviTRE);
        $totaleCostiVariabili += $totaleCostiVariabiliTRE;

        // Brembate ---------------------------------------------------------------------

        $totaleCostiVariabiliBRE = 0;
        $totaleRicaviBRE = 0;
        $totaleCostiFissiBRE = 0;
        $margineTotaleBRE = 0;
        $marginePercentualeBRE = 0;
        $ricaricoPercentualeBRE = 0;

        foreach ($riepilogo->getCostoVariabileBrembate() as $row) {
            $totaleCostiVariabiliBRE = trim($row['totalecostovariabile']);
        }

        foreach ($riepilogo->getRicavoVenditaProdottiBrembate() as $row) {
            $totaleRicaviBRE = trim($row['totalericavovendita']);
        }

        foreach ($riepilogo->getCostoFissoBrembate() as $row) {
            $totaleCostiFissiBRE = trim($row['totalecostofisso']);
        }

        $margineTotaleBRE = abs($totaleRicaviBRE) - $totaleCostiVariabiliBRE;
        if ($totaleRicaviBRE > parent::ZERO_VALUE) {
            $marginePercentualeBRE = ($margineTotaleBRE * 100 ) / abs($totaleRicaviBRE);
        }
        if ($totaleCostiVariabiliBRE > parent::ZERO_VALUE) {
            $ricaricoPercentualeBRE = ($margineTotaleBRE * 100) / abs($totaleCostiVariabiliBRE);
        }

        $totaleRicavi += abs($totaleRicaviBRE);
        $totaleCostiVariabili += $totaleCostiVariabiliBRE;

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
                "   <td class='text-right'>&euro; " . number_format(abs(floatval($totaleRicaviBRE)), 2, ',', '.') . "</td>" .
                "   <td class='text-right'>&euro; " . number_format(abs(floatval($totaleRicaviTRE)), 2, ',', '.') . "</td>" .
                "   <td class='text-right'>&euro; " . number_format(abs(floatval($totaleRicaviVIL)), 2, ',', '.') . "</td>" .
                "   <td class='bg-info text-right'>&euro; " . number_format(abs(floatval($totaleRicavi)), 2, ',', '.') . "</td>" .
                "</tr>" .
                "<tr>" .
                "   <td>%ml.acquisti%</td>" .
                "   <td class='text-right'>&euro; " . number_format(abs(floatval($totaleCostiVariabiliBRE)), 2, ',', '.') . "</td>" .
                "   <td class='text-right'>&euro; " . number_format(abs(floatval($totaleCostiVariabiliTRE)), 2, ',', '.') . "</td>" .
                "   <td class='text-right'>&euro; " . number_format(abs(floatval($totaleCostiVariabiliVIL)), 2, ',', '.') . "</td>" .
                "   <td class='bg-info text-right'>&euro; " . number_format(abs(floatval($totaleCostiVariabili)), 2, ',', '.') . "</td>" .
                "</tr>" .
                "<tr>" .
                "   <td>%ml.margineAssoluto%</td>" .
                "   <td class='text-right'>&euro; " . number_format(floatval($margineTotaleBRE), 2, ',', '.') . "</td>" .
                "   <td class='text-right'>&euro; " . number_format(floatval($margineTotaleTRE), 2, ',', '.') . "</td>" .
                "   <td class='text-right'>&euro; " . number_format(floatval($margineTotaleVIL), 2, ',', '.') . "</td>" .
                "   <td class='bg-info text-right'>&euro; " . number_format(floatval($margineTotale), 2, ',', '.') . "</td>" .
                "</tr>" .
                "<tr>" .
                "   <td>%ml.marginePercentuale%</td>" .
                "   <td class='text-right'>" . number_format(floatval($marginePercentualeBRE), 2, ',', '.') . " &#37;</td>" .
                "   <td class='text-right'>" . number_format(floatval($marginePercentualeTRE), 2, ',', '.') . " &#37;</td>" .
                "   <td class='text-right'>" . number_format(floatval($marginePercentualeVIL), 2, ',', '.') . " &#37;</td>" .
                "   <td class='bg-info text-right'>" . number_format(floatval($marginePercentuale), 2, ',', '.') . " &#37;</td>" .
                "</tr>" .
                "<tr>" .
                "   <td>%ml.ricaricoPercentuale%</td>" .
                "   <td class='text-right'>" . number_format(floatval($ricaricoPercentualeBRE), 2, ',', '.') . " &#37;</td>" .
                "   <td class='text-right'>" . number_format(floatval($ricaricoPercentualeTRE), 2, ',', '.') . " &#37;</td>" .
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

        // Trezzo ---------------------------------------------------------------------

        $totaleCostiVariabiliTRE = 0;
        $totaleRicaviTRE = 0;
        $totaleCostiFissiTRE = 0;
        $incidenzaCostiVariabiliSulFatturatoTRE = 0;
        $bepTRE = 0;

        foreach ($riepilogo->getCostoVariabileTrezzo() as $row) {
            $totaleCostiVariabiliTRE = trim($row['totalecostovariabile']);
        }

        foreach ($riepilogo->getRicavoVenditaProdottiTrezzo() as $row) {
            $totaleRicaviTRE = trim($row['totalericavovendita']);
        }

        foreach ($riepilogo->getCostoFissoTrezzo() as $row) {
            $totaleCostiFissiTRE = trim($row['totalecostofisso']);
        }

        if ($totaleRicaviTRE > parent::ZERO_VALUE) {
            $incidenzaCostiVariabiliSulFatturatoTRE = 1 - ($totaleCostiVariabiliTRE / abs($totaleRicaviTRE));
            $bepTRE = $totaleCostiFissiTRE / round($incidenzaCostiVariabiliSulFatturatoTRE, 2);
        }

        $totaleCostiVariabili += $totaleCostiVariabiliTRE;
        $totaleRicavi += $totaleRicaviTRE;
        $totaleCostiFissi += $totaleCostiFissiTRE;

        // Brembate ---------------------------------------------------------------------

        $totaleCostiVariabiliBRE = 0;
        $totaleRicaviBRE = 0;
        $totaleCostiFissiBRE = 0;
        $incidenzaCostiVariabiliSulFatturatoBRE = 0;
        $bepBRE = 0;

        foreach ($riepilogo->getCostoVariabileBrembate() as $row) {
            $totaleCostiVariabiliBRE = trim($row['totalecostovariabile']);
        }

        foreach ($riepilogo->getRicavoVenditaProdottiBrembate() as $row) {
            $totaleRicaviBRE = trim($row['totalericavovendita']);
        }

        foreach ($riepilogo->getCostoFissoBrembate() as $row) {
            $totaleCostiFissiBRE = trim($row['totalecostofisso']);
        }

        if ($totaleRicaviBRE > 0) {
            $incidenzaCostiVariabiliSulFatturatoBRE = 1 - ($totaleCostiVariabiliBRE / abs($totaleRicaviBRE));
            $bepBRE = $totaleCostiFissiBRE / round($incidenzaCostiVariabiliSulFatturatoBRE, 2);
        }

        $totaleCostiVariabili += $totaleCostiVariabiliBRE;
        $totaleRicavi += $totaleRicaviBRE;
        $totaleCostiFissi += $totaleCostiFissiBRE;

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
                "   <td class='text-right'>&euro; " . number_format(abs(floatval($totaleRicaviBRE)), 2, ',', '.') . "</td>" .
                "   <td class='text-right'>&euro; " . number_format(abs(floatval($totaleRicaviTRE)), 2, ',', '.') . "</td>" .
                "   <td class='text-right'>&euro; " . number_format(abs(floatval($totaleRicaviVIL)), 2, ',', '.') . "</td>" .
                "   <td class='bg-info text-right'>&euro; " . number_format(abs(floatval($totaleRicavi)), 2, ',', '.') . "</td>" .
                "</tr>" .
                "<tr>" .
                "   <td>Costi fissi</td>" .
                "   <td class='text-right'>&euro; " . number_format(abs(floatval($totaleCostiFissiBRE)), 2, ',', '.') . "</td>" .
                "   <td class='text-right'>&euro; " . number_format(abs(floatval($totaleCostiFissiTRE)), 2, ',', '.') . "</td>" .
                "   <td class='text-right'>&euro; " . number_format(abs(floatval($totaleCostiFissiVIL)), 2, ',', '.') . "</td>" .
                "   <td class='bg-info text-right'>&euro; " . number_format(abs(floatval($totaleCostiFissi)), 2, ',', '.') . "</td>" .
                "</tr>" .
                "<tr>" .
                "   <td>Acquisti</td>" .
                "   <td class='text-right'>&euro; " . number_format(abs(floatval($totaleCostiVariabiliBRE)), 2, ',', '.') . "</td>" .
                "   <td class='text-right'>&euro; " . number_format(abs(floatval($totaleCostiVariabiliTRE)), 2, ',', '.') . "</td>" .
                "   <td class='text-right'>&euro; " . number_format(abs(floatval($totaleCostiVariabiliVIL)), 2, ',', '.') . "</td>" .
                "   <td class='bg-info text-right'>&euro; " . number_format(abs(floatval($totaleCostiVariabili)), 2, ',', '.') . "</td>" .
                "</tr>" .
                "<tr>" .
                "   <td>Incidenza acquisti sul fatturato</td>" .
                "   <td class='text-right'> " . number_format(floatval($incidenzaCostiVariabiliSulFatturatoBRE), 2, ',', '.') . "</td>" .
                "   <td class='text-right'> " . number_format(floatval($incidenzaCostiVariabiliSulFatturatoTRE), 2, ',', '.') . "</td>" .
                "   <td class='text-right'> " . number_format(floatval($incidenzaCostiVariabiliSulFatturatoVIL), 2, ',', '.') . "</td>" .
                "   <td class='bg-info text-right'> " . number_format(floatval($incidenzaCostiVariabiliSulFatturato), 2, ',', '.') . "</td>" .
                "</tr>" .
                "<tr>" .
                "   <td>BEP</td>" .
                "   <td class='text-right'>&euro; " . number_format(floatval($bepBRE), 2, ',', '.') . "</td>" .
                "   <td class='text-right'>&euro; " . number_format(floatval($bepTRE), 2, ',', '.') . "</td>" .
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
                    "	<td class='text-right'>" . number_format(floatval($riepilogo->getTotaleRicaviBrembate()), 2, ',', '.') . "</td>" .
                    "	<td class='text-right'>" . number_format(floatval($riepilogo->getTotaleRicaviTrezzo()), 2, ',', '.') . "</td>" .
                    "	<td class='text-right'>" . number_format(floatval($riepilogo->getTotaleRicaviVilla()), 2, ',', '.') . "</td>" .
                    "	<td class='bg-info text-right'>" . number_format(floatval($riepilogo->getTotaleRicavi()), 2, ',', '.') . "</td>" .
                    "</tr>" .
                    "<tr>" .
                    "	<td>Totale Costi</td>" .
                    "	<td class='text-right'>" . number_format(floatval($riepilogo->getTotaleCostiBrembate()), 2, ',', '.') . "</td>" .
                    "	<td class='text-right'>" . number_format(floatval($riepilogo->getTotaleCostiTrezzo()), 2, ',', '.') . "</td>" .
                    "	<td class='text-right'>" . number_format(floatval($riepilogo->getTotaleCostiVilla()), 2, ',', '.') . "</td>" .
                    "	<td class='bg-info text-right'>" . number_format(floatval($riepilogo->getTotaleCosti()), 2, ',', '.') . "</td>" .
                    "</tr>";

            $utile_Bre = $riepilogo->getTotaleRicaviBrembate() - $riepilogo->getTotaleCostiBrembate();
            $utile_Tre = $riepilogo->getTotaleRicaviTrezzo() - $riepilogo->getTotaleCostiTrezzo();
            $utile_Vil = $riepilogo->getTotaleRicaviVilla() - $riepilogo->getTotaleCostiVilla();
            $utile = $utile_Bre + $utile_Tre + $utile_Vil;

            $risultato_esercizio .= "" .
                    "<tr>" .
                    "	<td>Utile del Periodo</td>" .
                    "	<td class='text-right'>" . number_format(floatval($utile_Bre), 2, ',', '.') . "</td>" .
                    "	<td class='text-right'>" . number_format(floatval($utile_Tre), 2, ',', '.') . "</td>" .
                    "	<td class='text-right'>" . number_format(floatval($utile_Vil), 2, ',', '.') . "</td>" .
                    "	<td class='bg-info text-right'>" . number_format(floatval($utile), 2, ',', '.') . "</td>" .
                    "</tr>" .
                    "</tbody></table>";
        } elseif ($tipoTotale == self::PERDITA) {

            $risultato_esercizio = $this->intestazioneTabellaRiepiloghiComparati();

            $risultato_esercizio .= "" .
                    "<tr>" .
                    "	<td>Totale Ricavi</td>" .
                    "	<td class='text-right'>" . number_format(floatval($riepilogo->getTotaleRicaviBrembate()), 2, ',', '.') . "</td>" .
                    "	<td class='text-right'>" . number_format(floatval($riepilogo->getTotaleRicaviTrezzo()), 2, ',', '.') . "</td>" .
                    "	<td class='text-right'>" . number_format(floatval($riepilogo->getTotaleRicaviVilla()), 2, ',', '.') . "</td>" .
                    "	<td class='bg-info text-right'>" . number_format(floatval($riepilogo->getTotaleRicavi()), 2, ',', '.') . "</td>" .
                    "</tr>" .
                    "<tr>" .
                    "	<td>Totale Costi</td>" .
                    "	<td class='text-right'>" . number_format(floatval($riepilogo->getTotaleCostiBrembate()), 2, ',', '.') . "</td>" .
                    "	<td class='text-right'>" . number_format(floatval($riepilogo->getTotaleCostiTrezzo()), 2, ',', '.') . "</td>" .
                    "	<td class='text-right'>" . number_format(floatval($riepilogo->getTotaleCostiVilla()), 2, ',', '.') . "</td>" .
                    "	<td class='bg-info text-right'>" . number_format(floatval($riepilogo->getTotaleCosti()), 2, ',', '.') . "</td>" .
                    "</tr>";

            $perdita_Bre = $riepilogo->getTotaleRicaviBrembate() - $riepilogo->getTotaleCostiBrembate();
            $perdita_Tre = $riepilogo->getTotaleRicaviTrezzo() - $riepilogo->getTotaleCostiTrezzo();
            $perdita_Vil = $riepilogo->getTotaleRicaviVilla() - $riepilogo->getTotaleCostiVilla();
            $perdita = $perdita_Bre + $perdita_Tre + $perdita_Vil;

            $risultato_esercizio .= "" .
                    "<tr>" .
                    "	<td>Perdita del Periodo</td>" .
                    "	<td class='text-right'>" . number_format(floatval($perdita_Bre), 2, ',', '.') . "</td>" .
                    "	<td class='text-right'>" . number_format(floatval($perdita_Tre), 2, ',', '.') . "</td>" .
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

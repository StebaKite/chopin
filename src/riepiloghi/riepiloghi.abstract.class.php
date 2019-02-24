<?php

require_once 'nexus6.abstract.class.php';
require_once 'riepiloghi.business.interface.php';

abstract class RiepiloghiAbstract extends Nexus6Abstract implements MainNexus6Interface {

    const BILANCIO = "Obj_bilancio";
    const PAGINA_ANDAMENTO_NEGOZI = "/riepiloghi/andamentoNegozi.form.html";
    const PAGINA_ANDAMENTO_MERCATI = "/riepiloghi/andamentoMercati.form.html";

    public static $messaggio;

    /*
     * Query ---------------------------------------------------------------
     */
//    public static $queryCreaRegistrazione = "/riepilogho/estraiRegistrazioniBilancio.sql";
//    public static $queryAndamentoCostiNegozio = "/riepiloghi/andamentoCostiNegozio.sql";
//    public static $queryAndamentoRicaviNegozio = "/riepiloghi/andamentoRicaviNegozio.sql";
    public static $queryAndamentoRicaviMercato = "/riepiloghi/andamentoRicaviMercato.sql";

    /*
     * Getters e Setters ---------------------------------------------------
     */

    public function setMessaggio($messaggio) {
        self::$messaggio = $messaggio;
    }

    public function getMessaggio() {
        return self::$messaggio;
    }

    /*
     * Metodi ---------------------------------
     */

    public function intestazioneTabellaBilancio() {
        return
                "<table class='table table-bordered table-hover'>" .
                "   <thead>" .
                "       <tr>" .
                "           <th width='300'>%ml.desconto%</th>" .
                "           <th width='550'>%ml.dessottoconto%</th>" .
                "           <th width='100' class='text-right'>%ml.importo%</th>" .
                "       </tr>" .
                "   </thead>" .
                "   <tbody id='myTable'>";
    }

    public function intestazioneTabellaRiepiloghiComparati() {
        return
                "<table class='table table-bordered table-hover'>" .
                "   <thead>" .
                "       <th width='300'>%ml.desconto%</th>" .
                "       <th width='100' class='text-right'>%ml.brembate%</th>" .
                "       <th width='100' class='text-right'>%ml.trezzo%</th>" .
                "       <th width='100' class='text-right'>%ml.villa%</th>" .
                "       <th width='100' class='text-right'>%ml.totale%</th>" .
                "   </thead>" .
                "   <tbody id='myTable'>";
    }

    public function intestazioneTabellaRiepiloghiAndamento() {
        return
                "<table class='table table-bordered table-hover'>" .
                "   <thead>" .
                "       <th width='200'>%ml.desconto%</th>" .
                "       <th width='50'>%ml.gen%</th>" .
                "       <th width='50'>%ml.feb%</th>" .
                "       <th width='50'>%ml.mar%</th>" .
                "       <th width='50'>%ml.apr%</th>" .
                "       <th width='50'>%ml.mag%</th>" .
                "       <th width='50'>%ml.giu%</th>" .
                "       <th width='50'>%ml.lug%</th>" .
                "       <th width='50'>%ml.ago%</th>" .
                "       <th width='50'>%ml.set%</th>" .
                "       <th width='50'>%ml.ott%</th>" .
                "       <th width='50'>%ml.nov%</th>" .
                "       <th width='50'>%ml.dic%</th>" .
                "       <th width='50'>%ml.totale%</th>" .
                "   </thead>" .
                "   <tbody id='myTable'>";
    }

//    /**
//     * Questo metodo estrae un riepilogo di totali per conto in Dare per mese
//     * @param unknown $utility
//     * @param unknown $db
//     * @param unknown $replace
//     * @return unknown
//     */
//    public function ricercaVociAndamentoCostiNegozioRiferimento($utility, $db, $replace) {
//
//        $array = $utility->getConfig();
//        $sqlTemplate = self::$root . $array['query'] . self::$queryAndamentoCostiNegozio;
//        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
//        $result = $db->getData($sql);
//
//        if ($result) {
//            if (pg_num_rows($result) > 0) {
//                $_SESSION['elencoVociAndamentoCostiNegozioRiferimento'] = $result;
//                $_SESSION['numCostiTrovatiRiferimento'] = pg_num_rows($result);
//            } else {
//                unset($_SESSION['elencoVociAndamentoCostiNegozioRiferimento']);
//                $_SESSION['numCostiTrovatiRiferimento'] = 0;
//            }
//            return $_SESSION['numCostiTrovatiRiferimento'];
//        } else
//            return "";
//    }
//    /**
//     * Questo metodo estrae un riepilogo di totali per conto in Avere per mese
//     * @param unknown $utility
//     * @param unknown $db
//     * @param unknown $replace
//     */
//    public function ricercaVociAndamentoRicaviNegozio($utility, $db, $replace) {
//
//        $array = $utility->getConfig();
//        $sqlTemplate = self::$root . $array['query'] . self::$queryAndamentoRicaviNegozio;
//        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
//        $result = $db->getData($sql);
//
//        if ($result) {
//            if (pg_num_rows($result) > 0) {
//                $_SESSION['elencoVociAndamentoRicaviNegozio'] = $result;
//                $_SESSION['numRicaviTrovati'] = pg_num_rows($result);
//            } else {
//                unset($_SESSION['elencoVociAndamentoRicaviNegozio']);
//                $_SESSION['numRicaviTrovati'] = 0;
//            }
//            return $_SESSION['numRicaviTrovati'];
//        } else
//            return "";
//    }

    /**
     * Questo metodo crea la tabella dei costi variabili di iniettare in pagina
     * @return string
     */
    public function makeCostiTable($bilancio) {

        $risultato_costi = "";

        if ($bilancio->getNumCostiTrovati() > 0) {

            $risultato_costi = $this->intestazioneTabellaBilancio();

            $numReg = 0;
            $totaleCosti = 0;
            $desconto_break = "";
            $ind_visibilita_sottoconti_break = "";
            $totaleConto = 0;

            foreach ($bilancio->getCostiBilancio() as $row) {

                $totaleSottoconto = trim($row['tot_conto']);
                $totaleCosti += $totaleSottoconto;

                $numReg ++;

                $importo = number_format($totaleSottoconto, 2, ',', '.');

                if (trim($row['des_conto']) != $desconto_break) {

                    if ($desconto_break != "") {

                        $totconto = number_format($totaleConto, 2, ',', '.');

                        if ($ind_visibilita_sottoconti_break === 'S') {
                            $risultato_costi .= "<tr>" .
                                    "	<td class='bg-info' colspan='2' align='right'></td>" .
                                    "	<td class='bg-info' align='right'>&euro; " . $totconto . "</td>" .
                                    "</tr>";
                        } else {
                            $risultato_costi .= "<tr>" .
                                    "	<td align='left'>" . $desconto_break . "</td>" .
                                    "	<td align='left'></td>" .
                                    "	<td align='right'>&euro; " . $totconto . "</td>" .
                                    "</tr>";
                        }

                        $totaleConto = 0;
                    }

                    if ($row['ind_visibilita_sottoconti'] == 'S') {
                        $risultato_costi .= "<tr>" .
                                "   <td align='left'>" . trim($row['des_conto']) . "</td>" .
                                "   <td align='left'>" . trim($row['des_sottoconto']) . "</td>" .
                                "   <td align='right'>&euro; " . $importo . "</td>" .
                                "</tr>";
                    }

                    $desconto_break = trim($row['des_conto']);
                    $ind_visibilita_sottoconti_break = $row['ind_visibilita_sottoconti'];
                } else {

                    if ($row['ind_visibilita_sottoconti'] == 'S') {
                        $risultato_costi .= "<tr>" .
                                "   <td align='left'></td>" .
                                "   <td align='left'>" . trim($row['des_sottoconto']) . "</td>" .
                                "   <td align='right'>&euro; " . $importo . "</td>" .
                                "</tr>";
                    }
                }
                $totaleConto += $totaleSottoconto;
            }

            $totconto = number_format($totaleConto, 2, ',', '.');

            if ($ind_visibilita_sottoconti_break == 'S') {
                $risultato_costi .= "<tr>" .
                        "   <td class='bg-info' colspan='2' align='right'></td>" .
                        "   <td class='bg-info' align='right'>&euro; " . $totconto . "</td>" .
                        "</tr>";
            } else {
                $risultato_costi .= "<tr>" .
                        "   <td align='left'>" . $desconto_break . "</td>" .
                        "   <td align='left'></td>" .
                        "   <td align='right'>&euro; " . $totconto . "</td>" .
                        "</tr>";
            }

            $bilancio->setNumCostiTrovati($numReg);
            $risultato_costi = $risultato_costi . "</tbody></table>";
            $bilancio->setTabellaCosti($risultato_costi);

            /**
             * Salvo il totale costi perchè servirà all'estrazione in PDF per stampare la tabella dei totali
             */
            $bilancio->setTotaleCosti($totaleCosti);
        }
        $_SESSION[self::BILANCIO] = serialize($bilancio);
    }

    public function makeRicaviTable($bilancio) {

        $risultato_ricavi = "";

        if ($bilancio->getNumRicaviTrovati() > 0) {

            $risultato_ricavi = $this->intestazioneTabellaBilancio();

            $numReg = 0;
            $desconto_break = "";
            $ind_visibilita_sottoconti_break = "";
            $totaleConto = 0;
            $totaleRicavi = 0;

            foreach ($bilancio->getRicaviBilancio() as $row) {

                $totaleSottoconto = trim($row['tot_conto']);
                $totaleRicavi += $totaleSottoconto;

                $numReg ++;

                $importo = number_format($totaleSottoconto * (-1), 2, ',', '.');

                if (trim($row['des_conto']) != $desconto_break) {

                    if ($desconto_break != "") {

                        $totconto = number_format($totaleConto * (-1), 2, ',', '.');

                        if ($ind_visibilita_sottoconti_break == 'S') {
                            $risultato_ricavi .= "<tr>" .
                                    "	<td class='bg-info' colspan='2' align='right'></td>" .
                                    "	<td class='bg-info' align='right'>&euro; " . $totconto . "</td>" .
                                    "</tr>";
                        } else {
                            $risultato_ricavi .= "<tr>" .
                                    "	<td align='left'>" . $desconto_break . "</td>" .
                                    "	<td align='left'></td>" .
                                    "	<td align='right'>&euro; " . $totconto . "</td>" .
                                    "</tr>";
                        }

                        $totaleConto = 0;
                    }

                    if ($row['ind_visibilita_sottoconti'] == 'S') {
                        $risultato_ricavi .= "<tr>" .
                                "   <td align='left'>" . trim($row['des_conto']) . "</td>" .
                                "   <td align='left'>" . trim($row['des_sottoconto']) . "</td>" .
                                "   <td align='right'>&euro; " . $importo . "</td>" .
                                "</tr>";
                    }

                    $desconto_break = trim($row['des_conto']);
                    $ind_visibilita_sottoconti_break = $row['ind_visibilita_sottoconti'];
                } else {

                    if ($row['ind_visibilita_sottoconti'] == 'S') {
                        $risultato_ricavi .= "<tr>" .
                                "   <td align='left'></td>" .
                                "   <td align='left'>" . trim($row['des_sottoconto']) . "</td>" .
                                "   <td align='right'>&euro; " . $importo . "</td>" .
                                "</tr>";
                    }
                }
                $totaleConto += $totaleSottoconto;
            }

            $totconto = number_format($totaleConto * (-1), 2, ',', '.');

            if ($ind_visibilita_sottoconti_break == 'S') {
                $risultato_ricavi .= "<tr>" .
                        "   <td class='bg-info' colspan='2' align='right'></td>" .
                        "   <td class='bg-info' width='108' align='right'>&euro; " . $totconto . "</td>" .
                        "</tr>";
            } else {
                $risultato_ricavi .= "<tr>" .
                        "   <td align='left'>" . $desconto_break . "</td>" .
                        "   <td align='left'></td>" .
                        "   <td align='right'>&euro; " . $totconto . "</td>" .
                        "</tr>";
            }

            $bilancio->setNumRicaviTrovati($numReg);
            $risultato_ricavi .= "</tbody></table>";
            $bilancio->setTabellaRicavi($risultato_ricavi);

            /**
             * Salvo il totale ricavi perchè servirà all'estrazione in PDF per stampare la tabella dei totali
             */
            $bilancio->setTotaleRicavi($totaleRicavi);
        }
        $_SESSION[self::BILANCIO] = serialize($bilancio);
    }

    public function makeAttivoTable($bilancio) {

        $risultato_attivo = "";
        $totaleAttivo = "";

        if ($bilancio->getNumAttivoTrovati() > 0) {

            $risultato_attivo = $this->intestazioneTabellaBilancio();

            $numReg = 0;
            $totaleAttivo = 0;
            $desconto_break = "";
            $ind_visibilita_sottoconti_break = "";
            $totaleConto = 0;

            foreach ($bilancio->getAttivoBilancio() as $row) {

                $totaleSottoconto = trim($row['tot_conto']);
                $totaleAttivo += $totaleSottoconto;

                $numReg ++;

                $importo = number_format(abs($totaleSottoconto), 2, ',', '.');

                if (trim($row['des_conto']) != $desconto_break) {

                    if ($desconto_break != "") {

                        $totconto = number_format(abs($totaleConto), 2, ',', '.');

                        if ($ind_visibilita_sottoconti_break == 'S') {
                            $risultato_attivo .= "<tr>" .
                                    "	<td class='bg-info' colspan='2' align='right'></td>" .
                                    "	<td class='bg-info' align='right'>&euro; " . $totconto . "</td>" .
                                    "</tr>";
                        } else {
                            $risultato_attivo .= "<tr>" .
                                    "	<td align='left'>" . $desconto_break . "</td>" .
                                    "	<td align='left'></td>" .
                                    "	<td align='right'>&euro; " . $totconto . "</td>" .
                                    "</tr>";
                        }

                        $totaleConto = 0;
                    }

                    if ($row['ind_visibilita_sottoconti'] == 'S') {
                        $risultato_attivo .= "<tr>" .
                                "   <td align='left'>" . trim($row['des_conto']) . "</td>" .
                                "   <td align='left'>" . trim($row['des_sottoconto']) . "</td>" .
                                "   <td align='right'>&euro; " . $importo . "</td>" .
                                "</tr>";
                    }

                    $desconto_break = trim($row['des_conto']);
                    $ind_visibilita_sottoconti_break = $row['ind_visibilita_sottoconti'];
                } else {

                    if ($row['ind_visibilita_sottoconti'] == 'S') {
                        $risultato_attivo .= "<tr>" .
                                "   <td align='left'></td>" .
                                "   <td align='left'>" . trim($row['des_sottoconto']) . "</td>" .
                                "   <td align='right'>&euro; " . $importo . "</td>" .
                                "</tr>";
                    }
                }
                $totaleConto += $totaleSottoconto;
            }

            $totconto = number_format(abs($totaleConto), 2, ',', '.');

            if ($ind_visibilita_sottoconti_break == 'S') {
                $risultato_attivo .= "<tr>" .
                        "   <td class='bg-info' colspan='2' align='right'></td>" .
                        "   <td class='bg-info' align='right'>&euro; " . $totconto . "</td>" .
                        "</tr>";
            } else {
                $risultato_attivo .= "<tr>" .
                        "   <td align='left'>" . $desconto_break . "</td>" .
                        "   <td align='left'></td>" .
                        "   <td align='right'>&euro; " . $totconto . "</td>" .
                        "</tr>";
            }

            $bilancio->setNumAttivoTrovati($numReg);
            $risultato_attivo .= "</tbody></table>";
            $bilancio->setTabellaAttivo($risultato_attivo);

            /**
             * Salvo il totale attivo perchè servirà all'estrazione in PDF per stampare la tabella dei totali
             */
            $bilancio->setTotaleAttivo($totaleAttivo);
        }
        $_SESSION[self::BILANCIO] = serialize($bilancio);
    }

    public function makePassivoTable($bilancio) {

        $risultato_passivo = "";
        $totalePassivo = "";

        if ($bilancio->getNumPassivoTrovati() > 0) {

            $risultato_passivo = $this->intestazioneTabellaBilancio();

            $numReg = 0;
            $totalePassivo = 0;
            $desconto_break = "";
            $ind_visibilita_sottoconti_break = "";
            $totaleConto = 0;

            foreach ($bilancio->getPassivoBilancio() as $row) {

                $totaleSottoconto = trim($row['tot_conto']);
                $totalePassivo += $totaleSottoconto;

                $numReg ++;

                $importo = number_format(abs($totaleSottoconto), 2, ',', '.');

                if (trim($row['des_conto']) != $desconto_break) {

                    if ($desconto_break != "") {

                        $totconto = number_format(abs($totaleConto), 2, ',', '.');

                        if ($ind_visibilita_sottoconti_break == 'S') {
                            $risultato_passivo .= "<tr>" .
                                    "	<td class='bg-info' colspan='2' align='right'></td>" .
                                    "	<td class='bg-info' align='right'>&euro; " . $totconto . "</td>" .
                                    "</tr>";
                        } else {
                            $risultato_passivo .= "<tr>" .
                                    "	<td align='left'>" . $desconto_break . "</td>" .
                                    "	<td align='left'></td>" .
                                    "	<td align='right'>&euro; " . $totconto . "</td>" .
                                    "</tr>";
                        }

                        $totaleConto = 0;
                    }

                    if ($row['ind_visibilita_sottoconti'] == 'S') {
                        $risultato_passivo .= "<tr>" .
                                "   <td align='left'>" . trim($row['des_conto']) . "</td>" .
                                "   <td align='left'>" . trim($row['des_sottoconto']) . "</td>" .
                                "   <td align='right'>&euro; " . $importo . "</td>" .
                                "</tr>";
                    }

                    $desconto_break = trim($row['des_conto']);
                    $ind_visibilita_sottoconti_break = $row['ind_visibilita_sottoconti'];
                } else {

                    if ($row['ind_visibilita_sottoconti'] == 'S') {
                        $risultato_passivo .= "<tr>" .
                                "   <td align='left'></td>" .
                                "   <td align='left'>" . trim($row['des_sottoconto']) . "</td>" .
                                "   <td align='right'>&euro; " . $importo . "</td>" .
                                "</tr>";
                    }
                }
                $totaleConto += $totaleSottoconto;
            }

            $totconto = number_format(abs($totaleConto), 2, ',', '.');

            if ($ind_visibilita_sottoconti_break == 'S') {
                $risultato_passivo .= "<tr>" .
                        "   <td class='bg-info' colspan='2' align='right'></td>" .
                        "   <td class='bg-info' align='right'>&euro; " . $totconto . "</td>" .
                        "</tr>";
            } else {
                $risultato_passivo .= "<tr>" .
                        "   <td align='left'>" . $desconto_break . "</td>" .
                        "   <td align='left'></td>" .
                        "   <td align='right'>&euro; " . $totconto . "</td>" .
                        "</tr>";
            }

            $bilancio->setNumPassivoTrovati($numReg);
            $risultato_passivo .= "</tbody></table>";
            $bilancio->setTabellaPassivo($risultato_passivo);

            /**
             * Salvo il totale passivo perchè servirà all'estrazione in PDF per stampare la tabella dei totali
             */
            $bilancio->setTotalePassivo($totalePassivo);
        }
        $_SESSION[self::BILANCIO] = serialize($bilancio);
    }

    public function makeTabs($bilancio) {

        $tabs = "";

        if (parent::isNotEmpty($bilancio->getTabellaCosti()) ||
                (parent::isNotEmpty($bilancio->getTabellaRicavi())) ||
                (parent::isNotEmpty($bilancio->getTabellaAttivo())) ||
                (parent::isNotEmpty($bilancio->getTabellaPassivo()))) {

            /**
             * Annotazione provvisoria per 2015, con il 2016 puoi buttarla via con la tab-6
             */
            $nota = "<br><p>Il bilancio di esercizio, <b>per il 2015</b>, viene generato partendo dal primo saldo disponibile: il <b>01/07/2015</b><br> " .
                    "<p>La funzione preleva un parametro dal config 'primoSaldoDisponibile = 01/07/2015' , in situazioni normali questo parametro non è " .
                    "valorizzato consentendo alla funzione il prelievo del primo saldo dell'anno al 01/01/2015</p>" .
                    "<p>Il bilancio periodico invece è funzionante e può essere estratto sempre tenendo presente la data del primo saldo o le " .
                    "eventuali successive.</p>";

            foreach ($bilancio->getCostiBilancio() as $row) {
                $totaleCostiBilancio += trim($row['tot_conto']);
            }

            foreach ($bilancio->getRicaviBilancio() as $row) {
                $totaleRicaviBilancio += trim($row['tot_conto']);
            }

            foreach ($bilancio->getCostoVariabile() as $row) {
                $totaleCostiVariabili = trim($row['totalecostovariabile']);
            }

            foreach ($bilancio->getRicavoVenditaProdotti() as $row) {
                $totaleRicavi = trim($row['totalericavovendita']);
            }

            foreach ($bilancio->getCostoFisso() as $row) {
                $totaleCostiFissi = trim($row['totalecostofisso']);
            }

            $totaleCosti = $totaleCostiFissi + $totaleCostiVariabili;

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
            $margineTotale = abs($totaleRicavi) - $totaleCostiVariabili;
            $marginePercentuale = ($margineTotale * 100 ) / abs($totaleRicavi);

            $incidenzaCostiVariabiliSulFatturato = 1 - ($totaleCostiVariabili / abs($totaleRicavi));
            $bep = $totaleCostiFissi / round($incidenzaCostiVariabiliSulFatturato, 2);

            $margineContribuzione = "" .
                    "<table class='table table-bordered table-hover'>" .
                    "   <tbody>" .
                    "       <tr height='30'>" .
                    "           <td width='308' align='left' class='bg-info'>Fatturato</td>" .
                    "           <td width='108' align='right' class='bg-info'>&euro; " . number_format(abs($totaleRicavi), 2, ',', '.') . "</td>" .
                    "       </tr>" .
                    "       <tr height='30'>" .
                    "           <td width='308' align='left' class='bg-info'>Costi variabili</td>" .
                    "           <td width='108' align='right' class='bg-info'>&euro; " . number_format(abs($totaleCostiVariabili), 2, ',', '.') . "</td>" .
                    "       </tr>" .
                    "       <tr height='30'>" .
                    "           <td width='308' align='left' class='bg-info'>Margine totale</td>" .
                    "           <td width='108' align='right' class='bg-info'>&euro; " . number_format($margineTotale, 2, ',', '.') . "</td>" .
                    "       </tr>" .
                    "       <tr height='30'>" .
                    "           <td width='308' align='left' class='bg-info'>Margine percentuale</td>" .
                    "           <td width='108' align='right' class='bg-info'>" . number_format($marginePercentuale, 2, ',', '.') . " &#37;</td>" .
                    "       </tr>" .
                    "   </tbody>" .
                    "</table>";

            $tabellaBep = "" .
                    "<table class='table table-bordered table-hover'>" .
                    "	<tbody>" .
                    "       <tr height='30'>" .
                    "           <td width='308' align='left' class='bg-info'>Fatturato</td>" .
                    "           <td width='108' align='right' class='bg-info'>&euro; " . number_format(abs($totaleRicavi), 2, ',', '.') . "</td>" .
                    "       </tr>" .
                    "       <tr height='30'>" .
                    "           <td width='308' align='left' class='bg-info'>Costi fissi</td>" .
                    "           <td width='108' align='right' class='bg-info'>&euro; " . number_format(abs($totaleCostiFissi), 2, ',', '.') . "</td>" .
                    "       </tr>" .
                    "       <tr height='30'>" .
                    "           <td width='308' align='left' class='bg-info'>Costi variabili</td>" .
                    "           <td width='108' align='right' class='bg-info'>&euro; " . number_format(abs($totaleCostiVariabili), 2, ',', '.') . "</td>" .
                    "       </tr>" .
                    "       <tr height='30'>" .
                    "           <td width='308' align='left' class='bg-info'>Incidenza costi variabili sul fatturato</td>" .
                    "           <td width='108' align='right' class='bg-info'> " . number_format($incidenzaCostiVariabiliSulFatturato, 2, ',', '.') . "</td>" .
                    "       </tr>" .
                    "       <tr height='30'>" .
                    "           <td width='308' align='left' class='bg-info'>BEP</td>" .
                    "           <td width='108' align='right' class='bg-info'>&euro; " . number_format($bep, 2, ',', '.') . "</td>" .
                    "       </tr>" .
                    "   </tbody>" .
                    "</table>";


            $notaMdc = "<br><p>Si definisce margine di contribuzione unitario la differenza tra il prezzo di vendita unitario ed il costo variabile unitario.</p>" .
                    "<p>Quando il margine di contribuzione del periodo è uguale al totale dei costi fissi del periodo si raggiunge il punto di pareggio.</p>" .
                    "<p>Quando il margine di contribuzione è maggiore dei costi fissi si genera l'utile.</p>" .
                    "<p>Il concetto di margine di contribuzione può essere utilizzato per una riclassificazione del conto economico utile a valutare l'effetto sul reddito di variazioni del volume di vendita o del fatturato. Tale riclassificazione si ottiene deducendo dai ricavi i costi variabili.</p>" .
                    "<p><strong>Ricavi - costi variabili= margine di contribuzione lordo di primo livello</strong></p><br>";

            $notaBep = "<br><p>Il calcolo del BEP per un’azienda che realizza prodotti si ottiene imponendo l’eguaglianza fra il fatturato totale e i costi totali ovvero : " .
                    "<strong>Fatturato totale = Costi totali</strong></p>" .
                    "<p>Metodo analitico: scrivendo le formule1 che esprimono i costi totali ed i ricavi, con qualche passaggio matematico è possibile determinare che si intersecano se: </p>" .
                    "<p><strong>BEP = CF / (1 – (CV / FAT))</strong></p>" .
                    "<ul>" .
                    "<li>FAT è il fatturato</li>" .
                    "<li>CF sono i costi fissi</li>" .
                    "<li>CV sono i costi variabili e quindi CV/FAT è l’incidenza dei costi variabili sul fatturato</li>" .
                    "<li>CT sono i costi totali e quindi CT = CF + CV</li>" .
                    "</ul><br>";

            $tabs = "<ul class='nav nav-tabs' role='tablist'>";

            if (parent::isNotEmpty($bilancio->getTabellaCosti())) {
                $tabs .= "<li role='presentation' class='active'><a href='#tabs-1' aria-controls='Costi' role='tab' data-toggle='tab'>Costi</a></li>";
            }
            if (parent::isNotEmpty($bilancio->getTabellaRicavi())) {
                $tabs .= "<li role='presentation'><a href='#tabs-2' aria-controls='Ricavi' role='tab' data-toggle='tab'>Ricavi</a></li>";
            }
            if (parent::isNotEmpty($bilancio->getTabellaAttivo())) {
                $tabs .= "<li role='presentation'><a href='#tabs-3' aria-controls='Attivo' role='tab' data-toggle='tab'>Attivo</a></li>";
            }
            if (parent::isNotEmpty($bilancio->getTabellaPassivo())) {
                $tabs .= "<li role='presentation'><a href='#tabs-4' aria-controls='Passivo' role='tab' data-toggle='tab'>Passivo</a></li>";
            }

            $tabs .= "<li role='presentation'><a href='#tabs-5' aria-controls='xxxx' role='tab' data-toggle='tab'>" . strtoupper($this->nomeTabTotali(abs($totaleRicaviBilancio), abs($totaleCostiBilancio))) . "</a></li>";
            $tabs .= "<li role='presentation'><a href='#tabs-6' aria-controls='MCT' role='tab' data-toggle='tab'>MCT</a></li>";
            $tabs .= "<li role='presentation'><a href='#tabs-7' aria-controls='BEP' role='tab' data-toggle='tab'>BEP</a></li>";
            $tabs .= "<li role='presentation'><a href='#tabs-8' aria-controls='Nota' role='tab' data-toggle='tab'>Nota importante</a></li>";
            $tabs .= "</ul>";

            $tabs .= "<div class='tab-content'>";

            if (parent::isNotEmpty($bilancio->getTabellaCosti())) {
                $tabs .= "<div role='tabpanel' class='tab-pane fade in active' id='tabs-1'>" . $bilancio->getTabellaCosti() . "</div>";
            }
            if (parent::isNotEmpty($bilancio->getTabellaRicavi())) {
                $tabs .= "<div role='tabpanel' class='tab-pane fade' id='tabs-2'>" . $bilancio->getTabellaRicavi() . "</div>";
            }
            if (parent::isNotEmpty($bilancio->getTabellaAttivo())) {
                $tabs .= "<div role='tabpanel' class='tab-pane fade' id='tabs-3'>" . $bilancio->getTabellaAttivo() . "</div>";
            }
            if (parent::isNotEmpty($bilancio->getTabellaPassivo())) {
                $tabs .= "<div role='tabpanel' class='tab-pane fade' id='tabs-4'>" . $bilancio->getTabellaPassivo() . "</div>";
            }

            $tabs .= "<div role='tabpanel' class='tab-pane fade' id='tabs-5'>" . $this->tabellaTotali($this->nomeTabTotali(abs($totaleRicaviBilancio), abs($totaleCostiBilancio)), abs($totaleRicaviBilancio), abs($totaleCostiBilancio)) . "</div>";
            $tabs .= "<div role='tabpanel' class='tab-pane fade' id='tabs-6'>" . $notaMdc . $margineContribuzione . "</div>";
            $tabs .= "<div role='tabpanel' class='tab-pane fade' id='tabs-7'>" . $notaBep . $tabellaBep . "</div>";
            $tabs .= "<div role='tabpanel' class='tab-pane fade' id='tabs-8'>" . $nota . "</div>";
            $tabs .= "</div>";
        }
        return $tabs;
    }

    public function makeTabsAndamentoNegozi($riepilogo) {

        if ((parent::isNotEmpty($riepilogo->getTableAndamentoCosti()) or ( parent::isNotEmpty($riepilogo->getTableAndamentoRicavi())) or ( parent::isNotEmpty($riepilogo->getTableUtilePerdita())) or ( parent::isNotEmpty($riepilogo->getTableMargineContribuzione())))) {

            $tabs = "<ul class='nav nav-tabs' role='tablist'>";

            if (parent::isNotEmpty($riepilogo->getTableAndamentoCosti())) {
                $tabs .= "<li role='presentation' class='active'><a href='#tabs-1' aria-controls='Costi' role='tab' data-toggle='tab'>Costi</a></li>";
            }
            if (parent::isNotEmpty($riepilogo->getTableAndamentoRicavi())) {
                $tabs .= "<li role='presentation'><a href='#tabs-2' aria-controls='Ricavi' role='tab' data-toggle='tab'>Ricavi</a></li>";
            }
            if (parent::isNotEmpty($riepilogo->getTableUtilePerdita())) {
                $tabs .= "<li role='presentation'><a href='#tabs-3' aria-controls='Attivo' role='tab' data-toggle='tab'>Utile</a></li>";
            }
            if (parent::isNotEmpty($riepilogo->getTableMargineContribuzione())) {
                $tabs .= "<li role='presentation'><a href='#tabs-4' aria-controls='Passivo' role='tab' data-toggle='tab'>MCT</a></li>";
            }

            $tabs .= "</ul>";

            $tabs .= "<div class='tab-content'>";

            if (parent::isNotEmpty($riepilogo->getTableAndamentoCosti())) {
                $tabs .= "<div role='tabpanel' class='tab-pane fade in active' id='tabs-1'>" . $riepilogo->getTableAndamentoCosti() . "</div>";
            }
            if (parent::isNotEmpty($riepilogo->getTableAndamentoRicavi())) {
                $tabs .= "<div role='tabpanel' class='tab-pane fade' id='tabs-2'>" . $riepilogo->getTableAndamentoRicavi() . "</div>";
            }
            if (parent::isNotEmpty($riepilogo->getTableUtilePerdita())) {
                $tabs .= "<div role='tabpanel' class='tab-pane fade' id='tabs-3'>" . $riepilogo->getTableUtilePerdita() . "</div>";
            }
            if (parent::isNotEmpty($riepilogo->getTableMargineContribuzione())) {
                $tabs .= "<div role='tabpanel' class='tab-pane fade' id='tabs-4'>" . $riepilogo->getTableMargineContribuzione() . "</div>";
            }

            $tabs .= "</div>";
        }
        return $tabs;
    }

    public function makeTabsAndamentoMercati($andamentoMercatiTables) {

        $tabs = "<ul class='nav nav-tabs' role='tablist'>";

        foreach ($andamentoMercatiTables as $key => $mercatoTable) {
            if ($key == self::VILLA) {
                $tabs .= "<li role='presentation' class='active'><a href='#tabs-" . $key . "' aria-controls='" . $key . "' role='tab' data-toggle='tab'>" . $key . "</a></li>";
            } else {
                $tabs .= "<li role='presentation'><a href='#tabs-" . $key . "' aria-controls='" . $key . "' role='tab' data-toggle='tab'>" . $key . "</a></li>";
            }
        }
        $tabs .= "</ul>";
        $tabs .= "<div class='tab-content'>";

        foreach ($andamentoMercatiTables as $key => $mercatoTable) {
            if ($key == self::VILLA) {
                $tabs .= "<div role='tabpanel' class='tab-pane fade in active' id='tabs-" . $key . "'>" . $mercatoTable . "</div>";
            } else {
                $tabs .= "<div role='tabpanel' class='tab-pane fade' id='tabs-" . $key . "'>" . $mercatoTable . "</div>";
            }
        }
        $tabs .= "</div>";

        return $tabs;
    }

    public function nomeTabTotali($totaleRicavi, $totaleCosti) {

        if ($totaleRicavi > $totaleCosti) {
            $nomeTabTotali = self::UTILE;
        } elseif ($totaleRicavi < $totaleCosti) {
            $nomeTabTotali = self::PERDITA;
        } else {
            $nomeTabTotali = self::PAREGGIO;
        }
        return $nomeTabTotali;
    }

    public function tabellaTotali($tipoTotale, $totaleRicavi, $totaleCosti) {

        if ($tipoTotale == "Utile") {

            $risultato_esercizio = "<table class='table table-bordered table-hover'><tbody>";

            $risultato_esercizio .= "<tr>" .
                    "	<td align='left' class='bg-info'>Totale Ricavi</td>" .
                    "	<td align='right' class='bg-info'>&euro; " . number_format(abs($totaleRicavi), 2, ',', '.') . "</td>" .
                    "</tr>";

            $risultato_esercizio .= "<tr>" .
                    "	<td align='left' class='bg-info'>Totale Costi</td>" .
                    "	<td align='right' class='bg-info'>&euro; " . number_format(abs($totaleCosti), 2, ',', '.') . "</td>" .
                    "</tr>";

            $utile = $totaleRicavi - $totaleCosti;

            $risultato_esercizio .= "<tr>" .
                    "	<td align='left' class='bg-info'>Utile del Periodo</td>" .
                    "	<td align='right' class='bg-info'>&euro; " . number_format($utile, 2, ',', '.') . "</td>" .
                    "</tr>";

            $risultato_esercizio .= "</tbody></table>";
        } elseif ($tipoTotale == "Perdita") {

            $risultato_esercizio = "<table class='table table-bordered table-hover'><tbody>";

            $risultato_esercizio .= "<tr>" .
                    "	<td align='left' class='bg-info'>Totale Ricavi</td>" .
                    "	<td align='right' class='bg-info'>&euro; " . number_format(abs($totaleRicavi), 2, ',', '.') . "</td>" .
                    "</tr>";

            $risultato_esercizio .= "<tr>" .
                    "	<td align='left' class='bg-info'>Totale Costi</td>" .
                    "	<td align='right' class='bg-info'>&euro; " . number_format(abs($totaleCosti), 2, ',', '.') . "</td>" .
                    "</tr>";

            $perdita = $totaleRicavi - $totaleCosti;

            $risultato_esercizio .= "<tr>" .
                    "	<td align='left' class='bg-info'>Perdita del Periodo</td>" .
                    "	<td align='right' class='bg-info'>&euro; " . number_format($perdita, 2, ',', '.') . "</td>" .
                    "</tr>";

            $risultato_esercizio .= "</tbody></table>";
        } else {

            $risultato_esercizio = "<br><table class='result'><tbody>";

            $risultato_esercizio .= "<tr height='30'>" .
                    "	<td width='308' align='left' class='mark'>Totale Ricavi</td>" .
                    "	<td width='108' align='right' class='mark'>" . number_format(abs($totaleRicavi), 2, ',', '.') . "</td>" .
                    "</tr>";

            $risultato_esercizio .= "<tr height='30'>" .
                    "	<td width='308' align='left' class='mark'>Totale Costi</td>" .
                    "	<td width='108' align='right' class='mark'>" . number_format(abs($totaleCosti), 2, ',', '.') . "</td>" .
                    "</tr>";

            $pareggio = $totaleRicavi - $totaleCosti;

            $risultato_esercizio .= "<tr height='30'>" .
                    "	<td width='308' align='left' class='mark'>Utile del Periodo</td>" .
                    "	<td width='108' align='right' class='mark'>" . number_format($pareggio, 2, ',', '.') . "</td>" .
                    "</tr>";

            $risultato_esercizio .= "</tbody></table>";
        }
        return $risultato_esercizio;
    }

    public function makeDeltaCosti() {

        $deltaCosti = array();
        unset($_SESSION["elencoVociDeltaCostiNegozio"]);

        if (isset($_SESSION["elencoVociAndamentoCostiNegozio"])) {

            $vociCosto = pg_fetch_all($_SESSION["elencoVociAndamentoCostiNegozio"]);
            $vociCostoRif = pg_fetch_all($_SESSION["elencoVociAndamentoCostiNegozioRiferimento"]);

            /**
             * Vengono riportate solo le voci di costo presenti nell'elenco del periodo corrente
             * Ogni voce presente nel periodo corrente viene cercata nel periodo di riferimento
             * L'importo della voce di riferimento viene sottratto all'importo della voe corrente
             */
            foreach ($vociCosto as $voce) {

                $desConto = trim($voce['des_conto']);
                $mm_registrazione = trim($voce['mm_registrazione']);
                $ind_gruppo = trim($voce['ind_gruppo']);

                $importo = $voce['tot_conto'];
                $importoRiferimento = $this->getImportoVoce($vociCostoRif, $desConto, $ind_gruppo, $mm_registrazione);

                $deltaVoce = array(
                    'des_conto' => $desConto,
                    'mm_registrazione' => $mm_registrazione,
                    'ind_gruppo' => $ind_gruppo,
                    'tot_conto' => abs($importo) - abs($importoRiferimento)
                );

                array_push($deltaCosti, $deltaVoce);
            }
            $_SESSION["elencoVociDeltaCostiNegozio"] = $deltaCosti;
        }
    }

    public function makeDeltaRicavi() {

        $deltaRicavi = array();
        unset($_SESSION["elencoVociDeltaRicaviNegozio"]);

        if (isset($_SESSION["elencoVociAndamentoRicaviNegozio"])) {

            $vociRicavo = pg_fetch_all($_SESSION["elencoVociAndamentoRicaviNegozio"]);
            $vociRicavoRif = pg_fetch_all($_SESSION["elencoVociAndamentoRicaviNegozioRiferimento"]);

            /**
             * Vengono riportate solo le voci di ricavo presenti nell'elenco del periodo corrente
             * Ogni voce presente nel periodo corrente viene cercata nel periodo di riferimento
             * L'importo della voce di riferimento viene sottratto all'importo della voe corrente
             */
            foreach ($vociRicavo as $voce) {

                $desConto = trim($voce['des_conto']);
                $mm_registrazione = trim($voce['mm_registrazione']);
                $ind_gruppo = trim($voce['ind_gruppo']);

                $importo = $voce['tot_conto'];
                $importoRiferimento = $this->getImportoVoce($vociRicavoRif, $desConto, $ind_gruppo, $mm_registrazione);

                $deltaVoce = array(
                    'des_conto' => $desConto,
                    'mm_registrazione' => $mm_registrazione,
                    'ind_gruppo' => $ind_gruppo,
                    'tot_conto' => abs($importo) - abs($importoRiferimento)
                );

                array_push($deltaRicavi, $deltaVoce);
            }
            $_SESSION["elencoVociDeltaRicaviNegozio"] = $deltaRicavi;
        }
    }

    public function getImportoVoce($vociRif, $desConto, $ind_gruppo, $mm_registrazione) {

        $tot_conto = 0;

        foreach ($vociRif as $voceRif) {

            if ((trim($voceRif['des_conto']) === $desConto) and ( trim($voceRif['mm_registrazione']) === $mm_registrazione) and ( trim($voceRif['ind_gruppo']) === $ind_gruppo)) {
                $tot_conto = $voceRif['tot_conto'];
            }
        }
        return $tot_conto;
    }

    public function makeAndamentoCostiTable($riepilogo) {

        $vociAndamento = $riepilogo->getCostiAndamentoNegozio();
        $risultato_andamento = $this->intestazioneTabellaRiepiloghiAndamento();

        $desconto_break = "";
        $totaliMesi = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        $totaliComplessiviMesi = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

        /**
         * Salvo i totali che mi occorrono per il calcolo dell'MCT per mese
         */
        $totaliAcquistiMesi = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

        foreach ($vociAndamento as $row) {

            $totconto = $row['tot_conto'];

            if (trim($row['des_conto']) != $desconto_break) {

                if ($desconto_break != "") {

                    /**
                     * A rottura creo le colonne accumulate e inizializzo l'array
                     */
                    $totale_conto = 0;

                    for ($i = 1; $i < 13; $i++) {
                        if ($totaliMesi[$i] == 0)
                            $risultato_andamento .= "<td>&ndash;&ndash;&ndash;</td>";
                        else
                            $risultato_andamento .= "<td>" . number_format($totaliMesi[$i], 0, ',', '.') . "</td>";
                        $totale_conto = $totale_conto + $totaliMesi[$i];
                    }
                    $risultato_andamento .= "<td class='bg-info'>" . number_format($totale_conto, 0, ',', '.') . "</td>";

                    $risultato_andamento .= "</tr>";
                    for ($i = 1; $i < 13; $i++) {
                        $totaliMesi[$i] = 0;
                    }

                    $risultato_andamento .= "<tr><td>" . trim($row['des_conto']) . "</td>";
                    $totaliMesi[$row['mm_registrazione']] = $totconto;
                    $totaliComplessiviMesi[$row['mm_registrazione']] += $totconto;
                } else {
                    $risultato_andamento .= "<tr><td>" . trim($row['des_conto']) . "</td>";
                    $totaliComplessiviMesi[$row['mm_registrazione']] += $totconto;
                    $totaliMesi[$row['mm_registrazione']] = $totconto;
                }
                $desconto_break = trim($row['des_conto']);
                if (trim($row['ind_gruppo'] === "CV")) {
                    $totaliAcquistiMesi[$row['mm_registrazione']] += $totconto;
                }
            } else {
                $totaliMesi[$row['mm_registrazione']] += $totconto;
                $totaliComplessiviMesi[$row['mm_registrazione']] += $totconto;
                if (trim($row['ind_gruppo'] === "CV")) {
                    $totaliAcquistiMesi[$row['mm_registrazione']] += $totconto;
                }
            }
        }

        /**
         * Ultima riga
         */
        $totale_conto = 0;

        for ($i = 1; $i < 13; $i++) {
            if ($totaliMesi[$i] == 0)
                $risultato_andamento .= "<td>&ndash;&ndash;&ndash;</td>";
            else
                $risultato_andamento .= "<td>" . number_format($totaliMesi[$i], 0, ',', '.') . "</td>";
            $totale_conto = $totale_conto + $totaliMesi[$i];
        }
        $risultato_andamento .= "<td class='bg-info'>" . number_format($totale_conto, 0, ',', '.') . "</td>";

        $risultato_andamento .= "</tr>";
        $risultato_andamento .= "<tr><td class='bg-info'>%ml.totale%</td>";

        /**
         * Totali mensili finali
         */
        $totale_anno = 0;

        for ($i = 1; $i < 13; $i++) {
            if ($totaliComplessiviMesi[$i] == 0)
                $risultato_andamento .= "<td class='bg-info'>&ndash;&ndash;&ndash;</td>";
            else
                $risultato_andamento .= "<td class='bg-info'>" . number_format($totaliComplessiviMesi[$i], 0, ',', '.') . "</td>";
            $totale_anno = $totale_anno + $totaliComplessiviMesi[$i];
        }
        $risultato_andamento .= "<td class='bg-info'>" . number_format($totale_anno, 0, ',', '.') . "</td>";
        $risultato_andamento .= "</tr></tbody></table>";

        $riepilogo->setTotaliAcquistiMesi($totaliAcquistiMesi);
        $riepilogo->setTotaliComplessiviAcquistiMesi($totaliComplessiviMesi);
        $riepilogo->setTableAndamentoCosti($risultato_andamento);
        $_SESSION[self::RIEPILOGO] = serialize($riepilogo);
    }

    public function makeAndamentoRicaviTable($vociAndamento) {

        $risultato_andamento = "";

        if (count($vociAndamento) > 0) {

            $risultato_andamento = "<table class='result'>" .
                    "	<thead>" .
                    "		<th width='200'>%ml.desconto%</th>" .
                    "		<th width='50'>%ml.gen%</th>" .
                    "		<th width='50'>%ml.feb%</th>" .
                    "		<th width='50'>%ml.mar%</th>" .
                    "		<th width='50'>%ml.apr%</th>" .
                    "		<th width='50'>%ml.mag%</th>" .
                    "		<th width='50'>%ml.giu%</th>" .
                    "		<th width='50'>%ml.lug%</th>" .
                    "		<th width='50'>%ml.ago%</th>" .
                    "		<th width='50'>%ml.set%</th>" .
                    "		<th width='50'>%ml.ott%</th>" .
                    "		<th width='50'>%ml.nov%</th>" .
                    "		<th width='50'>%ml.dic%</th>" .
                    "		<th width='50'>%ml.totale%</th>" .
                    "	</thead>" .
                    "</table>" .
                    "<div class='scroll-bilancio'>" .
                    "	<table class='result'>" .
                    "		<tbody>";

            $desconto_break = "";
            $totaliMesi = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);     // dodici mesi
            $totaliComplessiviMesi = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);  // dodici mesi

            /**
             * Salvo i totali che mi occorrono per il calcolo dell'MCT per mese
             */
            $totaliRicaviMesi = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

            foreach ($vociAndamento as $row) {

                $totconto = $row['tot_conto'];

                if (trim($row['des_conto']) != $desconto_break) {

                    if ($desconto_break != "") {

                        /**
                         * A rottura creo le colonne accumulate e inizializzo l'array
                         */
                        $totale_conto = 0;

                        for ($i = 1; $i < 13; $i++) {
                            if ($totaliMesi[$i] == 0)
                                $risultato_andamento .= "<td width='58' align='right'>&ndash;&ndash;&ndash;</td>";
                            else
                                $risultato_andamento .= "<td width='58' align='right'>" . number_format(abs($totaliMesi[$i]), 0, ',', '.') . "</td>";
                            $totale_conto = $totale_conto + $totaliMesi[$i];
                        }
                        $risultato_andamento .= "<td class='mark' width='58' align='right'>" . number_format(abs($totale_conto), 0, ',', '.') . "</td>";

                        $risultato_andamento .= "</tr>";
                        for ($i = 1; $i < 13; $i++) {
                            $totaliMesi[$i] = 0;
                        }

                        $risultato_andamento .= "<tr><td width='208' align='left'>" . trim($row['des_conto']) . "</td>";
                        $totaliMesi[$row['mm_registrazione']] = $totconto;
                        $totaliComplessiviMesi[$row['mm_registrazione']] += $totconto;
                    } else {
                        $risultato_andamento .= "<tr><td width='208' align='left'>" . trim($row['des_conto']) . "</td>";
                        $totaliMesi[$row['mm_registrazione']] = $totconto;
                        $totaliComplessiviMesi[$row['mm_registrazione']] += $totconto;
                    }
                    $desconto_break = trim($row['des_conto']);
                    if (trim($row['ind_gruppo'] == "RC"))
                        $totaliRicaviMesi[$row['mm_registrazione']] += $totconto;
                }
                else {
                    $totaliMesi[$row['mm_registrazione']] += $totconto;
                    $totaliComplessiviMesi[$row['mm_registrazione']] += $totconto;
                    if (trim($row['ind_gruppo'] == "RC"))
                        $totaliRicaviMesi[$row['mm_registrazione']] += $totconto;
                }
            }

            /**
             * Ultima riga
             */
            $totale_conto = 0;

            for ($i = 1; $i < 13; $i++) {
                if ($totaliMesi[$i] == 0)
                    $risultato_andamento .= "<td width='58' align='right'>&ndash;&ndash;&ndash;</td>";
                else
                    $risultato_andamento .= "<td width='58' align='right'>" . number_format(abs($totaliMesi[$i]), 0, ',', '.') . "</td>";
                $totale_conto = $totale_conto + $totaliMesi[$i];
            }
            $risultato_andamento .= "<td class='mark' width='58' align='right'>" . number_format(abs($totale_conto), 0, ',', '.') . "</td>";

            $risultato_andamento .= "</tr>";
            $risultato_andamento .= "<tr><td class='enlarge' width='208' align='left'>%ml.totale%</td>";

            /**
             * Totali mensili finali
             */
            for ($i = 1; $i < 13; $i++) {
                if ($totaliComplessiviMesi[$i] == 0)
                    $risultato_andamento .= "<td width='58' align='right'>&ndash;&ndash;&ndash;</td>";
                else
                    $risultato_andamento .= "<td class='mark' width='58' align='right'>" . number_format(abs($totaliComplessiviMesi[$i]), 0, ',', '.') . "</td>";
                $totale_anno = $totale_anno + $totaliComplessiviMesi[$i];
            }
            $risultato_andamento .= "<td class='mark' width='58' align='right'>" . number_format(abs($totale_anno), 0, ',', '.') . "</td>";
            $risultato_andamento .= "</tr></tbody></table></div>";
        }

        /**
         * Metto in sessione i totali per mese dei ricavi che mi occorrono per creare le tabella dell'MCT progressivo
         */
        $_SESSION["totaliRicaviMesi"] = $totaliRicaviMesi;
        $_SESSION["totaliComplessiviRicaviMesi"] = $totaliComplessiviMesi;

        return $risultato_andamento;
    }

    public function makeAndamentoCostiDeltaTable($vociAndamento) {

        $risultato_andamento = "";

        if (count($vociAndamento) > 0) {

            $risultato_andamento = "<table class='result'>" .
                    "	<thead>" .
                    "		<th width='200'>%ml.desconto%</th>" .
                    "		<th width='50'>%ml.gen%</th>" .
                    "		<th width='50'>%ml.feb%</th>" .
                    "		<th width='50'>%ml.mar%</th>" .
                    "		<th width='50'>%ml.apr%</th>" .
                    "		<th width='50'>%ml.mag%</th>" .
                    "		<th width='50'>%ml.giu%</th>" .
                    "		<th width='50'>%ml.lug%</th>" .
                    "		<th width='50'>%ml.ago%</th>" .
                    "		<th width='50'>%ml.set%</th>" .
                    "		<th width='50'>%ml.ott%</th>" .
                    "		<th width='50'>%ml.nov%</th>" .
                    "		<th width='50'>%ml.dic%</th>" .
                    "		<th width='50'>%ml.totale%</th>" .
                    "	</thead>" .
                    "</table>" .
                    "<div class='scroll-bilancio'>" .
                    "	<table class='result'>" .
                    "		<tbody>";

            $desconto_break = "";
            $totaliMesi = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            $totaliComplessiviMesi = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

            /**
             * Salvo i totali che mi occorrono per il calcolo dell'MCT per mese
             */
            $totaliAcquistiMesi = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

            foreach ($vociAndamento as $row) {

                $totconto = $row['tot_conto'];

                if (trim($row['des_conto']) != $desconto_break) {

                    if ($desconto_break != "") {

                        /**
                         * A rottura creo le colonne accumulate e inizializzo l'array
                         */
                        $totale_conto = 0;

                        for ($i = 1; $i < 13; $i++) {
                            if ($totaliMesi[$i] == 0)
                                $risultato_andamento .= "<td width='58' align='right'>&ndash;&ndash;&ndash;</td>";
                            else
                                $risultato_andamento .= "<td width='58' align='right'>" . number_format($totaliMesi[$i], 0, ',', '.') . "</td>";
                            $totale_conto = $totale_conto + $totaliMesi[$i];
                        }
                        $risultato_andamento .= "<td class='mark' width='58' align='right'>" . number_format($totale_conto, 0, ',', '.') . "</td>";

                        $risultato_andamento .= "</tr>";
                        for ($i = 1; $i < 13; $i++) {
                            $totaliMesi[$i] = 0;
                        }

                        $risultato_andamento .= "<tr><td width='208' align='left'>" . trim($row['des_conto']) . "</td>";
                        $totaliMesi[$row['mm_registrazione']] = $totconto;
                        $totaliComplessiviMesi[$row['mm_registrazione']] += $totconto;
                    } else {
                        $risultato_andamento .= "<tr><td width='208' align='left'>" . trim($row['des_conto']) . "</td>";
                        $totaliComplessiviMesi[$row['mm_registrazione']] += $totconto;
                        $totaliMesi[$row['mm_registrazione']] = $totconto;
                    }
                    $desconto_break = trim($row['des_conto']);
                    if (trim($row['ind_gruppo'] === "CV")) {
                        $totaliAcquistiMesi[$row['mm_registrazione']] += $totconto;
                    }
                } else {
                    $totaliMesi[$row['mm_registrazione']] += $totconto;
                    $totaliComplessiviMesi[$row['mm_registrazione']] += $totconto;
                    if (trim($row['ind_gruppo'] === "CV")) {
                        $totaliAcquistiMesi[$row['mm_registrazione']] += $totconto;
                    }
                }
            }

            /**
             * Ultima riga
             */
            $totale_conto = 0;

            for ($i = 1; $i < 13; $i++) {
                if ($totaliMesi[$i] == 0)
                    $risultato_andamento .= "<td width='58' align='right'>&ndash;&ndash;&ndash;</td>";
                else
                    $risultato_andamento .= "<td width='58' align='right'>" . number_format($totaliMesi[$i], 0, ',', '.') . "</td>";
                $totale_conto = $totale_conto + $totaliMesi[$i];
            }
            $risultato_andamento .= "<td class='mark' width='58' align='right'>" . number_format($totale_conto, 0, ',', '.') . "</td>";

            $risultato_andamento .= "</tr>";
            $risultato_andamento .= "<tr><td class='enlarge' width='208' align='left'>%ml.totale%</td>";

            /**
             * Totali mensili finali
             */
            $totale_anno = 0;

            for ($i = 1; $i < 13; $i++) {
                if ($totaliComplessiviMesi[$i] == 0)
                    $risultato_andamento .= "<td width='58' align='right'>&ndash;&ndash;&ndash;</td>";
                else
                    $risultato_andamento .= "<td class='mark' width='58' align='right'>" . number_format($totaliComplessiviMesi[$i], 0, ',', '.') . "</td>";
                $totale_anno = $totale_anno + $totaliComplessiviMesi[$i];
            }
            $risultato_andamento .= "<td class='mark' width='58' align='right'>" . number_format($totale_anno, 0, ',', '.') . "</td>";
            $risultato_andamento .= "</tr></tbody></table></div>";
        }

        /**
         * Metto in sessione i totali per mese degli acquisti che mi occorrono per creare le tabella dell'MCT progressivo
         */
        $_SESSION["totaliAcquistiMesi"] = $totaliAcquistiMesi;
        $_SESSION["totaliComplessiviAcquistiMesi"] = $totaliComplessiviMesi;

        return $risultato_andamento;
    }

    public function makeAndamentoRicaviDeltaTable($riepilogo) {

        $vociAndamento = $riepilogo->getRicaviAndamentoNegozio();
        $risultato_andamento = $this->intestazioneTabellaRiepiloghiAndamento();

        $desconto_break = "";
        $totaliMesi = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);     // dodici mesi
        $totaliComplessiviMesi = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);  // dodici mesi

        /**
         * Salvo i totali che mi occorrono per il calcolo dell'MCT per mese
         */
        $totaliRicaviMesi = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

        foreach ($vociAndamento as $row) {

            $totconto = $row['tot_conto'];

            if (trim($row['des_conto']) != $desconto_break) {

                if ($desconto_break != "") {

                    /**
                     * A rottura creo le colonne accumulate e inizializzo l'array
                     */
                    $totale_conto = 0;

                    for ($i = 1; $i < 13; $i++) {
                        if ($totaliMesi[$i] == 0)
                            $risultato_andamento .= "<td>&ndash;&ndash;&ndash;</td>";
                        else
                            $risultato_andamento .= "<td>" . number_format(abs($totaliMesi[$i]), 0, ',', '.') . "</td>";
                        $totale_conto = $totale_conto + $totaliMesi[$i];
                    }
                    $risultato_andamento .= "<td class='bg-info'>" . number_format(abs($totale_conto), 0, ',', '.') . "</td>";

                    $risultato_andamento .= "</tr>";
                    for ($i = 1; $i < 13; $i++) {
                        $totaliMesi[$i] = 0;
                    }

                    $risultato_andamento .= "<tr><td>" . trim($row['des_conto']) . "</td>";
                    $totaliMesi[$row['mm_registrazione']] = $totconto;
                    $totaliComplessiviMesi[$row['mm_registrazione']] += $totconto;
                } else {
                    $risultato_andamento .= "<tr><td>" . trim($row['des_conto']) . "</td>";
                    $totaliMesi[$row['mm_registrazione']] = $totconto;
                    $totaliComplessiviMesi[$row['mm_registrazione']] += $totconto;
                }
                $desconto_break = trim($row['des_conto']);
                if (trim($row['ind_gruppo'] == "RC"))
                    $totaliRicaviMesi[$row['mm_registrazione']] += $totconto;
            }
            else {
                $totaliMesi[$row['mm_registrazione']] += $totconto;
                $totaliComplessiviMesi[$row['mm_registrazione']] += $totconto;
                if (trim($row['ind_gruppo'] == "RC"))
                    $totaliRicaviMesi[$row['mm_registrazione']] += $totconto;
            }
        }

        /**
         * Ultima riga
         */
        $totale_conto = 0;

        for ($i = 1; $i < 13; $i++) {
            if ($totaliMesi[$i] == 0)
                $risultato_andamento .= "<td>&ndash;&ndash;&ndash;</td>";
            else
                $risultato_andamento .= "<td>" . number_format($totaliMesi[$i], 0, ',', '.') . "</td>";
            $totale_conto = $totale_conto + $totaliMesi[$i];
        }
        $risultato_andamento .= "<td class='bg-info'>" . number_format($totale_conto, 0, ',', '.') . "</td>";

        $risultato_andamento .= "</tr>";
        $risultato_andamento .= "<tr><td>%ml.totale%</td>";

        /**
         * Totali mensili finali
         */
        for ($i = 1; $i < 13; $i++) {
            if ($totaliComplessiviMesi[$i] == 0)
                $risultato_andamento .= "<td class='bg-info'>&ndash;&ndash;&ndash;</td>";
            else
                $risultato_andamento .= "<td class='bg-info'>" . number_format($totaliComplessiviMesi[$i], 0, ',', '.') . "</td>";
            $totale_anno = $totale_anno + $totaliComplessiviMesi[$i];
        }
        $risultato_andamento .= "<td class='bg-info'>" . number_format($totale_anno, 0, ',', '.') . "</td>";
        $risultato_andamento .= "</tr></tbody></table>";

        $riepilogo->setTotaliRicaviMesi($totaliRicaviMesi);
        $riepilogo->setTotaliComplessiviRicaviMesi($totaliComplessiviMesi);
        $riepilogo->setTableAndamentoRicavi($risultato_andamento);
        $_SESSION[self::RIEPILOGO] = serialize($riepilogo);
    }

    /**
     * Questo metodo costruisce una tabella html per l'utile o la perdita progressiva del mese
     * @param unknown $totaliAcquistiMesi
     * @param unknown $totaliRicaviMesi
     */
    public function makeUtilePerditaTable($riepilogo) {

        $totaliAcquistiMesi = $riepilogo->getTotaliAcquistiMesi();
        $totaliRicaviMesi = $riepilogo->getTotaliRicaviMesi();

        $utilePerdita = $this->intestazioneTabellaRiepiloghiAndamento();
        $totaleUtilePerdita = 0;

        $utilePerditaMesi = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);              // dodici mesi
        $classe = array('', '', '', '', '', '', '', '', '', '', '', '');
        $progrUtilePerditaMesi = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        $progrClasse = array('', '', '', '', '', '', '', '', '', '', '', '');

        /**
         * Calcolo l'utile o la perdita per ciascun mese
         */
        for ($i = 1; $i < 13; $i++) {
            $utilePerditaMesi[$i] = abs($totaliRicaviMesi[$i]) - $totaliAcquistiMesi[$i];
            if ($utilePerditaMesi[$i] < 0) {
                $classe[$i] = "class='bg-warning'";
            }

            $totaleUtilePerdita = $totaleUtilePerdita + $utilePerditaMesi[$i];

            for ($j = $i; $j < 13; $j++) {
                if ($utilePerditaMesi[$i] > 0) {
                    $progrUtilePerditaMesi[$j] += $utilePerditaMesi[$i];
                    if ($progrUtilePerditaMesi[$j] < 0) {
                        $progrClasse[$j] = "class='bg-warning'";
                    } else {
                        $progrClasse[$j] = "";
                    }
                }
                else {
                    $progrUtilePerditaMesi[$j] = 0;
                }                
            }
        }

        if ($totaleUtilePerdita < 0)
            $class = "class='bg-warning'";
        else
            $class = "class='bg-info'";

        $utilePerdita .= "" .
                "<tr>" .
                "   <td>%ml.utilePerdita%</td>" .
                "   <td " . $classe[1] . ">" . number_format($utilePerditaMesi[1], 0, ',', '.') . "</td>" .
                "   <td " . $classe[2] . ">" . number_format($utilePerditaMesi[2], 0, ',', '.') . "</td>" .
                "   <td " . $classe[3] . ">" . number_format($utilePerditaMesi[3], 0, ',', '.') . "</td>" .
                "   <td " . $classe[4] . ">" . number_format($utilePerditaMesi[4], 0, ',', '.') . "</td>" .
                "   <td " . $classe[5] . ">" . number_format($utilePerditaMesi[5], 0, ',', '.') . "</td>" .
                "   <td " . $classe[6] . ">" . number_format($utilePerditaMesi[6], 0, ',', '.') . "</td>" .
                "   <td " . $classe[7] . ">" . number_format($utilePerditaMesi[7], 0, ',', '.') . "</td>" .
                "   <td " . $classe[8] . ">" . number_format($utilePerditaMesi[8], 0, ',', '.') . "</td>" .
                "   <td " . $classe[9] . ">" . number_format($utilePerditaMesi[9], 0, ',', '.') . "</td>" .
                "   <td " . $classe[10] . ">" . number_format($utilePerditaMesi[10], 0, ',', '.') . "</td>" .
                "   <td " . $classe[11] . ">" . number_format($utilePerditaMesi[11], 0, ',', '.') . "</td>" .
                "   <td " . $classe[12] . ">" . number_format($utilePerditaMesi[12], 0, ',', '.') . "</td>" .
                "   <td " . $class . ">" . number_format($totaleUtilePerdita, 0, ',', '.') . "</td>" .
                "</tr>" .
                "<tr>" .
                "   <td>%ml.progrUtilePerdita%</td>" .
                "   <td " . $progrClasse[1] . ">" . number_format($progrUtilePerditaMesi[1], 0, ',', '.') . "</td>" .
                "   <td " . $progrClasse[2] . ">" . number_format($progrUtilePerditaMesi[2], 0, ',', '.') . "</td>" .
                "   <td " . $progrClasse[3] . ">" . number_format($progrUtilePerditaMesi[3], 0, ',', '.') . "</td>" .
                "   <td " . $progrClasse[4] . ">" . number_format($progrUtilePerditaMesi[4], 0, ',', '.') . "</td>" .
                "   <td " . $progrClasse[5] . ">" . number_format($progrUtilePerditaMesi[5], 0, ',', '.') . "</td>" .
                "   <td " . $progrClasse[6] . ">" . number_format($progrUtilePerditaMesi[6], 0, ',', '.') . "</td>" .
                "   <td " . $progrClasse[7] . ">" . number_format($progrUtilePerditaMesi[7], 0, ',', '.') . "</td>" .
                "   <td " . $progrClasse[8] . ">" . number_format($progrUtilePerditaMesi[8], 0, ',', '.') . "</td>" .
                "   <td " . $progrClasse[9] . ">" . number_format($progrUtilePerditaMesi[9], 0, ',', '.') . "</td>" .
                "   <td " . $progrClasse[10] . ">" . number_format($progrUtilePerditaMesi[10], 0, ',', '.') . "</td>" .
                "   <td " . $progrClasse[11] . ">" . number_format($progrUtilePerditaMesi[11], 0, ',', '.') . "</td>" .
                "   <td " . $progrClasse[12] . ">" . number_format($progrUtilePerditaMesi[12], 0, ',', '.') . "</td>" .
                "   <td></td>" .
                "</tr>" .
                "</tbody></table>";

        $riepilogo->setTableUtilePerdita($utilePerdita);
        $_SESSION[self::RIEPILOGO] = serialize($riepilogo);
    }

    /**
     * Questo metodo costruisce una tabella html per i risultati del calcolo dell' MCT progressivo per mese
     * @param unknown $costoVariabile
     * @param unknown $ricavoVendita
     * @param unknown $costoFisso
     */
    public function makeTableMargineContribuzioneAndamentoNegozi($riepilogo) {

        $totaliAcquistiMesi = $riepilogo->getTotaliAcquistiMesi();
        $totaliRicaviMesi = $riepilogo->getTotaliRicaviMesi();

        $margineContribuzione = $this->intestazioneTabellaRiepiloghiAndamento();
        $totaleRicavi = 0;
        $totaleAcquisti = 0;
        $totaliMctAssolutoMesi = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);     // dodici mesi
        $totaliMctPercentualeMesi = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);     // dodici mesi
        $totaliMctRicaricoMesi = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);     // dodici mesi
        $totaleMctAssoluto = 0;
        $totaleMctPercentuale = 0;
        $totaleMctRicarico = 0;
        $classe_MctAss = array('', '', '', '', '', '', '', '', '', '', '', '');     // dodici mesi
        $classe_MctPer = array('', '', '', '', '', '', '', '', '', '', '', '');     // dodici mesi
        $classe_MctRic = array('', '', '', '', '', '', '', '', '', '', '', '');     // dodici mesi
        $classe_tot_MctAss = "";
        $classe_tot_MctPer = "";
        $classe_tot_MctRic = "";

        /**
         * Faccio i totali di linea annuali per Acquisti e Ricavi
         */
        for ($i = 1; $i < 13; $i++) {
            $totaleRicavi += $totaliRicaviMesi[$i];
            $totaleAcquisti += $totaliAcquistiMesi[$i];
        }

        /**
         * Calcolo gli MCT per ciascun mese
         */
        for ($i = 1; $i < 13; $i++) {

            $totaliMctAssolutoMesi[$i] = abs($totaliRicaviMesi[$i]) - $totaliAcquistiMesi[$i];
            if ($totaliMctAssolutoMesi[$i] < 0)
                $classe_MctAss[$i] = "class='bg-warning'";

            /**
             * Se il ricavo è zero non faccio la divisione
             */
            if ($totaliRicaviMesi[$i] != 0) {
                $totaliMctPercentualeMesi[$i] = ($totaliMctAssolutoMesi[$i] * 100 ) / abs($totaliRicaviMesi[$i]);
            } else {
                $totaliMctPercentualeMesi[$i] = ($totaliMctAssolutoMesi[$i] * 100 );
            }

            if ($totaliMctPercentualeMesi[$i] < 0)
                $classe_MctPer[$i] = "class='bg-warning'";

            /**
             * Se il totale acquisti è zero non faccio la divisione
             */
            if ($totaliAcquistiMesi[$i] != 0) {
                $totaliMctRicaricoMesi[$i] = ($totaliMctAssolutoMesi[$i] * 100) / abs($totaliAcquistiMesi[$i]);
            } else {
                $totaliMctRicaricoMesi[$i] = ($totaliMctAssolutoMesi[$i] * 100);
            }

            if ($totaliMctRicaricoMesi[$i] < 0)
                $classe_MctRic[$i] = "class='bg-warning'";
        }

        /**
         * Faccio il totale di linea annuale per il margine assoluto
         */
        for ($i = 1; $i < 13; $i++) {
            $totaleMctAssoluto += $totaliMctAssolutoMesi[$i];
        }
        if ($totaleMctAssoluto < 0)
            $classe_tot_MctAss = "class='bg-warning'";
        else
            $classe_tot_MctAss = "class=''";

        /**
         * Calcolo i margini sui totali annuali
         */
        $totaleMctPercentuale = ($totaleMctAssoluto * 100 ) / abs($totaleRicavi);
        if ($totaleMctPercentuale < 0)
            $classe_tot_MctPer = "class='bg-warning'";
        else
            $classe_tot_MctPer = "class=''";

        $totaleMctRicarico = ($totaleMctAssoluto * 100) / abs($totaleAcquisti);
        if ($totaleMctRicarico < 0)
            $classe_tot_MctRic = "class='bg-warning'";
        else
            $classe_tot_MctRic = "class=''";

        $margineContribuzione .= "" .
                "<tr>" .
                "   <td>%ml.fatturato%</td>" .
                "   <td>" . number_format(abs($totaliRicaviMesi[1]), 0, ',', '.') . "</td>" .
                "   <td>" . number_format(abs($totaliRicaviMesi[2]), 0, ',', '.') . "</td>" .
                "   <td>" . number_format(abs($totaliRicaviMesi[3]), 0, ',', '.') . "</td>" .
                "   <td>" . number_format(abs($totaliRicaviMesi[4]), 0, ',', '.') . "</td>" .
                "   <td>" . number_format(abs($totaliRicaviMesi[5]), 0, ',', '.') . "</td>" .
                "   <td>" . number_format(abs($totaliRicaviMesi[6]), 0, ',', '.') . "</td>" .
                "   <td>" . number_format(abs($totaliRicaviMesi[7]), 0, ',', '.') . "</td>" .
                "   <td>" . number_format(abs($totaliRicaviMesi[8]), 0, ',', '.') . "</td>" .
                "   <td>" . number_format(abs($totaliRicaviMesi[9]), 0, ',', '.') . "</td>" .
                "   <td>" . number_format(abs($totaliRicaviMesi[10]), 0, ',', '.') . "</td>" .
                "   <td>" . number_format(abs($totaliRicaviMesi[11]), 0, ',', '.') . "</td>" .
                "   <td>" . number_format(abs($totaliRicaviMesi[12]), 0, ',', '.') . "</td>" .
                "   <td class='bg-info'>" . number_format(abs($totaleRicavi), 0, ',', '.') . "</td>" .
                "</tr>" .
                "<tr>" .
                "   <td>%ml.acquisti%</td>" .
                "   <td>" . number_format(abs($totaliAcquistiMesi[1]), 0, ',', '.') . "</td>" .
                "   <td>" . number_format(abs($totaliAcquistiMesi[2]), 0, ',', '.') . "</td>" .
                "   <td>" . number_format(abs($totaliAcquistiMesi[3]), 0, ',', '.') . "</td>" .
                "   <td>" . number_format(abs($totaliAcquistiMesi[4]), 0, ',', '.') . "</td>" .
                "   <td>" . number_format(abs($totaliAcquistiMesi[5]), 0, ',', '.') . "</td>" .
                "   <td>" . number_format(abs($totaliAcquistiMesi[6]), 0, ',', '.') . "</td>" .
                "   <td>" . number_format(abs($totaliAcquistiMesi[7]), 0, ',', '.') . "</td>" .
                "   <td>" . number_format(abs($totaliAcquistiMesi[8]), 0, ',', '.') . "</td>" .
                "   <td>" . number_format(abs($totaliAcquistiMesi[9]), 0, ',', '.') . "</td>" .
                "   <td>" . number_format(abs($totaliAcquistiMesi[10]), 0, ',', '.') . "</td>" .
                "   <td>" . number_format(abs($totaliAcquistiMesi[11]), 0, ',', '.') . "</td>" .
                "   <td>" . number_format(abs($totaliAcquistiMesi[12]), 0, ',', '.') . "</td>" .
                "   <td class='bg-info'>" . number_format(abs($totaleAcquisti), 0, ',', '.') . "</td>" .
                "</tr>" .
                "<tr>" .
                "   <td>%ml.margineAssoluto%</td>" .
                "   <td " . $classe_MctAss[1] . ">" . number_format($totaliMctAssolutoMesi[1], 0, ',', '.') . "</td>" .
                "   <td " . $classe_MctAss[2] . ">" . number_format($totaliMctAssolutoMesi[2], 0, ',', '.') . "</td>" .
                "   <td " . $classe_MctAss[3] . ">" . number_format($totaliMctAssolutoMesi[3], 0, ',', '.') . "</td>" .
                "   <td " . $classe_MctAss[4] . ">" . number_format($totaliMctAssolutoMesi[4], 0, ',', '.') . "</td>" .
                "   <td " . $classe_MctAss[5] . ">" . number_format($totaliMctAssolutoMesi[5], 0, ',', '.') . "</td>" .
                "   <td " . $classe_MctAss[6] . ">" . number_format($totaliMctAssolutoMesi[6], 0, ',', '.') . "</td>" .
                "   <td " . $classe_MctAss[7] . ">" . number_format($totaliMctAssolutoMesi[7], 0, ',', '.') . "</td>" .
                "   <td " . $classe_MctAss[8] . ">" . number_format($totaliMctAssolutoMesi[8], 0, ',', '.') . "</td>" .
                "   <td " . $classe_MctAss[9] . ">" . number_format($totaliMctAssolutoMesi[9], 0, ',', '.') . "</td>" .
                "   <td " . $classe_MctAss[10] . ">" . number_format($totaliMctAssolutoMesi[10], 0, ',', '.') . "</td>" .
                "   <td " . $classe_MctAss[11] . ">" . number_format($totaliMctAssolutoMesi[11], 0, ',', '.') . "</td>" .
                "   <td " . $classe_MctAss[12] . ">" . number_format($totaliMctAssolutoMesi[12], 0, ',', '.') . "</td>" .
                "   <td class='bg-info'>" . number_format($totaleMctAssoluto, 0, ',', '.') . "</td>" .
                "</tr>" .
                "<tr>" .
                "   <td>%ml.marginePercentuale%</td>" .
                "   <td " . $classe_MctPer[1] . ">" . number_format($totaliMctPercentualeMesi[1], 0, ',', '.') . "</td>" .
                "   <td " . $classe_MctPer[2] . ">" . number_format($totaliMctPercentualeMesi[2], 0, ',', '.') . "</td>" .
                "   <td " . $classe_MctPer[3] . ">" . number_format($totaliMctPercentualeMesi[3], 0, ',', '.') . "</td>" .
                "   <td " . $classe_MctPer[4] . ">" . number_format($totaliMctPercentualeMesi[4], 0, ',', '.') . "</td>" .
                "   <td " . $classe_MctPer[5] . ">" . number_format($totaliMctPercentualeMesi[5], 0, ',', '.') . "</td>" .
                "   <td " . $classe_MctPer[6] . ">" . number_format($totaliMctPercentualeMesi[6], 0, ',', '.') . "</td>" .
                "   <td " . $classe_MctPer[7] . ">" . number_format($totaliMctPercentualeMesi[7], 0, ',', '.') . "</td>" .
                "   <td " . $classe_MctPer[8] . ">" . number_format($totaliMctPercentualeMesi[8], 0, ',', '.') . "</td>" .
                "   <td " . $classe_MctPer[9] . ">" . number_format($totaliMctPercentualeMesi[9], 0, ',', '.') . "</td>" .
                "   <td " . $classe_MctPer[10] . ">" . number_format($totaliMctPercentualeMesi[10], 0, ',', '.') . "</td>" .
                "   <td " . $classe_MctPer[11] . ">" . number_format($totaliMctPercentualeMesi[11], 0, ',', '.') . "</td>" .
                "   <td " . $classe_MctPer[12] . ">" . number_format($totaliMctPercentualeMesi[12], 0, ',', '.') . "</td>" .
                "   <td class='bg-info'>" . number_format($totaleMctPercentuale, 0, ',', '.') . "</td>" .
                "</tr>" .
                "<tr>" .
                "   <td>%ml.ricaricoPercentuale%</td>" .
                "   <td " . $classe_MctRic[1] . ">" . number_format($totaliMctRicaricoMesi[1], 0, ',', '.') . "</td>" .
                "   <td " . $classe_MctRic[2] . ">" . number_format($totaliMctRicaricoMesi[2], 0, ',', '.') . "</td>" .
                "   <td " . $classe_MctRic[3] . ">" . number_format($totaliMctRicaricoMesi[3], 0, ',', '.') . "</td>" .
                "   <td " . $classe_MctRic[4] . ">" . number_format($totaliMctRicaricoMesi[4], 0, ',', '.') . "</td>" .
                "   <td " . $classe_MctRic[5] . ">" . number_format($totaliMctRicaricoMesi[5], 0, ',', '.') . "</td>" .
                "   <td " . $classe_MctRic[6] . ">" . number_format($totaliMctRicaricoMesi[6], 0, ',', '.') . "</td>" .
                "   <td " . $classe_MctRic[7] . ">" . number_format($totaliMctRicaricoMesi[7], 0, ',', '.') . "</td>" .
                "   <td " . $classe_MctRic[8] . ">" . number_format($totaliMctRicaricoMesi[8], 0, ',', '.') . "</td>" .
                "   <td " . $classe_MctRic[9] . ">" . number_format($totaliMctRicaricoMesi[9], 0, ',', '.') . "</td>" .
                "   <td " . $classe_MctRic[10] . ">" . number_format($totaliMctRicaricoMesi[10], 0, ',', '.') . "</td>" .
                "   <td " . $classe_MctRic[11] . ">" . number_format($totaliMctRicaricoMesi[11], 0, ',', '.') . "</td>" .
                "   <td " . $classe_MctRic[12] . ">" . number_format($totaliMctRicaricoMesi[12], 0, ',', '.') . "</td>" .
                "   <td class='bg-info'>" . number_format($totaleMctRicarico, 0, ',', '.') . "</td>" .
                "</tr>" .
                "</tbody></table>";

        $riepilogo->setTableMargineContribuzione($margineContribuzione);
        $_SESSION[self::RIEPILOGO] = serialize($riepilogo);
    }

    public function makeAndamentoRicaviMercatoTable($vociAndamento) {

        $risultato_andamento = "";

        if (count($vociAndamento) > 0) {

            $risultato_andamento = "" .
                    "<table class='table table-bordered table-hover'>" .
                    "	<thead>" .
                    "		<th width='200'>%ml.desmercato%</th>" .
                    "		<th width='50'>%ml.gen%</th>" .
                    "		<th width='50'>%ml.feb%</th>" .
                    "		<th width='50'>%ml.mar%</th>" .
                    "		<th width='50'>%ml.apr%</th>" .
                    "		<th width='50'>%ml.mag%</th>" .
                    "		<th width='50'>%ml.giu%</th>" .
                    "		<th width='50'>%ml.lug%</th>" .
                    "		<th width='50'>%ml.ago%</th>" .
                    "		<th width='50'>%ml.set%</th>" .
                    "		<th width='50'>%ml.ott%</th>" .
                    "		<th width='50'>%ml.nov%</th>" .
                    "		<th width='50'>%ml.dic%</th>" .
                    "		<th width='50'>%ml.totale%</th>" .
                    "	</thead>" .
                    "   <tbody>";

            $desmercato_break = "";
            $totaliMesi = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);     // dodici mesi
            $totaliComplessiviMesi = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);  // dodici mesi

            /**
             * Salvo i totali che mi occorrono per il calcolo dell'MCT per mese
             */
            $totaliRicaviMesi = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

            foreach ($vociAndamento as $row) {

                $totmercato = $row['imp_ricavo_mercato'];

                if (trim($row['des_mercato']) != $desmercato_break) {

                    if ($desmercato_break != "") {

                        /**
                         * A rottura creo le colonne accumulate e inizializzo l'array
                         */
                        $totale_mercato = 0;

                        for ($i = 1; $i < 13; $i++) {
                            if ($totaliMesi[$i] == 0)
                                $risultato_andamento .= "<td align='right'>&ndash;&ndash;&ndash;</td>";
                            else
                                $risultato_andamento .= "<td align='right'>" . number_format(abs($totaliMesi[$i]), 0, ',', '.') . "</td>";
                            $totale_mercato += $totaliMesi[$i];
                        }
                        $risultato_andamento .= "<td class='bg-info' align='right'>" . number_format(abs($totale_mercato), 0, ',', '.') . "</td>";

                        $risultato_andamento .= "</tr>";
                        for ($i = 1; $i < 13; $i++) {
                            $totaliMesi[$i] = 0;
                        }

                        $risultato_andamento .= "<tr><td>" . trim($row['des_mercato']) . "</td>";
                        $totaliMesi[$row['mm_registrazione']] = $totmercato;
                        $totaliComplessiviMesi[$row['mm_registrazione']] += $totmercato;
                    } else {
                        $risultato_andamento .= "<tr><td>" . trim($row['des_mercato']) . "</td>";
                        $totaliMesi[$row['mm_registrazione']] = $totmercato;
                        $totaliComplessiviMesi[$row['mm_registrazione']] += $totmercato;
                    }
                    $desmercato_break = trim($row['des_mercato']);
                } else {
                    $totaliMesi[$row['mm_registrazione']] += $totmercato;
                    $totaliComplessiviMesi[$row['mm_registrazione']] += $totmercato;
                }
            }

            /**
             * Ultima riga
             */
            $totale_mercato = 0;

            for ($i = 1; $i < 13; $i++) {
                if ($totaliMesi[$i] == 0)
                    $risultato_andamento .= "<td align='right'>&ndash;&ndash;&ndash;</td>";
                else
                    $risultato_andamento .= "<td align='right'>" . number_format(abs($totaliMesi[$i]), 0, ',', '.') . "</td>";
                $totale_mercato += $totaliMesi[$i];
            }
            $risultato_andamento .= "<td class='bg-info' align='right'>" . number_format(abs($totale_mercato), 0, ',', '.') . "</td>";

            $risultato_andamento .= "</tr>";
            $risultato_andamento .= "<tr><td class='bg-info' align='left'>%ml.totale%</td>";

            /**
             * Totali mensili finali
             */
            for ($i = 1; $i < 13; $i++) {
                if ($totaliComplessiviMesi[$i] == 0)
                    $risultato_andamento .= "<td class='bg-info' align='right'>&ndash;&ndash;&ndash;</td>";
                else
                    $risultato_andamento .= "<td class='bg-info' align='right'>" . number_format(abs($totaliComplessiviMesi[$i]), 0, ',', '.') . "</td>";
                $totale_anno = $totale_anno + $totaliComplessiviMesi[$i];
            }
            $risultato_andamento .= "<td class='bg-info' align='right'>" . number_format(abs($totale_anno), 0, ',', '.') . "</td>";
            $risultato_andamento .= "</tr></tbody></table>";
        }

        return $risultato_andamento;
    }

}
?>


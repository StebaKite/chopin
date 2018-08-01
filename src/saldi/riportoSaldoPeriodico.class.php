<?php

require_once 'saldi.abstract.class.php';
require_once 'utility.class.php';
require_once 'nexus6.main.interface.php';
require_once 'lavoroPianificato.class.php';
require_once 'conto.class.php';
require_once 'saldo.class.php';

/**
 * Questa classe è rieseguibile.
 * Se un saldo per un conto e una data c'è già in tabella viene aggiornato altrimenti viene inserito
 * @author stefano
 *
 */
class RiportoSaldoPeriodico extends SaldiAbstract implements MainNexus6Interface {

//    public static $messaggio;
    public static $querySaldoConto = "/saldi/saldoConto.sql";

    function __construct() {
//        self::$root = '/var/www/html';
    }

    public function getInstance() {
        if (!isset($_SESSION[self::RIPORTO_SALDO]))
            $_SESSION[self::SALDO] = serialize(new RiportoSaldoPeriodico());
        return unserialize($_SESSION[self::RIPORTO_SALDO]);
    }

    public function start($db, $pklavoro) {

        static $mese = array(
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

        $riportoStatoPatrimoniale_Ok = FALSE;
        $riportoContoEconomico_Ok = FALSE;

        $lavoroPianificato = LavoroPianificato::getInstance();
        $conto = Conto::getInstance();
        $utility = Utility::getInstance();
        $config = $utility->getConfig();

        $negozi = explode(',', $config['negozi']);

        /**
         * Determino il mese da estrarre rispetto alla data di esecuzione del lavoro pianificato
         */
        $dataGenerazioneSaldo = $lavoroPianificato->getDatLavoro();
        $dataEstrazioneRegistrazioni = date("Y/m/d", strtotime('-1 month', strtotime($lavoroPianificato->getDatLavoro())));

        $dataLavoro = explode("/", $dataEstrazioneRegistrazioni);
        $mesePrecedente = str_pad($dataLavoro[1], 2, "0", STR_PAD_LEFT);
        $descrizioneSaldo = "Riporto saldo di " . $mese[$mesePrecedente];

        $anno = ($mesePrecedente == 12) ? date("Y", strtotime('-1 year', strtotime($lavoroPianificato->getDatLavoro()))) : date("Y", strtotime($lavoroPianificato->getDatLavoro()));

        if ($this->isAnnoBisestile($anno)) {
            $ggMese = array(
                '01' => '31', '02' => '29', '03' => '31', '04' => '30', '05' => '31', '06' => '30',
                '07' => '31', '08' => '31', '09' => '30', '10' => '31', '11' => '30', '12' => '31'
            );
        } else {
            $ggMese = array(
                '01' => '31', '02' => '28', '03' => '31', '04' => '30', '05' => '31', '06' => '30',
                '07' => '31', '08' => '31', '09' => '30', '10' => '31', '11' => '30', '12' => '31'
            );
        }

        /**
         * Riporto stato patrimoniale
         */
        if ($conto->leggiStatoPatrimoniale($db)) {

            $this->riportoStatoPatrimoniale($db, $lavoroPianificato, $utility, $negozi, $conto, $mesePrecedente, $anno, $dataGenerazioneSaldo, $descrizioneSaldo, $ggMese);

            $da = '01/' . $mesePrecedente . '/' . $anno;
            $a = $ggMese[$mesePrecedente] . '/' . $mesePrecedente . '/' . $anno;

            $riportoStatoPatrimoniale_Ok = TRUE;
        }

        /**
         * Riporto conto economico.
         * Il primo riporto dell'anno non viene fatto. I conti ripartono da zero.
         */
        if (date("m/d", strtotime($_SESSION["dataEsecuzioneLavoro"])) != "01/01") {

            $result = $this->prelevaContoEconomico($db, $utility);

            if ($result) {

                $this->riportoContoEconomico($db, $pklavoro, $utility, $negozi, $result, $mesePrecedente, $anno, $dataGenerazioneSaldo, $descrizioneSaldo, $ggMese);

                $da = '01/' . $mesePrecedente . '/' . $anno;
                $a = $ggMese[$mesePrecedente] . '/' . $mesePrecedente . '/' . $anno;

                $riportoContoEconomico_Ok = TRUE;
            }
        }

        /**
         * Se uno dei due riporti è andato bene considero il lavoro eseguito
         */
        if (($riportoStatoPatrimoniale_Ok) or ( $riportoContoEconomico_Ok)) {
            $this->cambioStatoLavoroPianificato($db, $utility, $pklavoro, '10');
            return TRUE;
        } else
            return FALSE;
    }

    private function riportoStatoPatrimoniale($db, $lavoroPianificato, $utility, $negozi, $conto, $mesePrecedente, $anno, $dataGenerazioneSaldo, $descrizioneSaldo, $ggMese) {

        $config = $utility->getConfig();

        /**
         * Tutti i conti dello Stato Patrimoniale
         */
        foreach ($conto->getContiStatoPatrimoniale() as $conto) {

            /**
             * Per ciascun conto effettuo la totalizzazione delle registrazioni per ciascun negozio
             */
//            $dareAvere_conto = ($conto[Conto::TIP_CONTO] = "Avere") ? "A" : "D";  // prelevo il tipo del conto Dare/Avere

            $saldo = Saldo::getInstance();

            foreach ($negozi as $negozio) {

                $saldo->setDataregDA('01/' . $mesePrecedente . '/' . $anno);
                $saldo->setDataregA($ggMese[$mesePrecedente] . '/' . $mesePrecedente . '/' . $anno);
                $saldo->setCodNegozio($negozio);
                $saldo->setCodConto($conto[Conto::COD_CONTO]);
                $saldo->setCodSottoconto($conto[Sottoconto::COD_SOTTOCONTO]);
                $_SESSION[self::SALDO] = serialize($saldo);

                if ($saldo->leggiSaldoConto($db)) {

                    $totale_conto = 0; //default
                    $dareAvere = ""; //default

                    /**
                     * Faccio la somma algebrica di tutti i totali estratti.
                     * Normalmente dalla query di totalizzazione viene fuori una riga con un totale, ma nel caso
                     * di conti con importo negativo e segno contrario escono due righe.
                     *
                     */
                    foreach (pg_fetch_all($result) as $row) {
                        $totale_conto = $totale_conto + $row[Conto::TOT_CONTO];
                    }

                    /**
                     * L'attribuzione del segno viene fatta osservanto il totale ottenuto dalla somma algebrica degli importi
                     */
                    $dareAvere = ($totale_conto > 0) ? "D" : "A";

                    $saldo->setDatSaldo($dataGenerazioneSaldo);
                    $saldo->setDesSaldo($descrizioneSaldo);
                    $saldo->setImpSaldo(abs($totale_conto));
                    $saldo->setIndDareavere($dareAvere);
                    $_SESSION[self::SALDO] = serialize($saldo);

                    $this->gestioneSaldo($db);
                }
            }
        }
    }

    private function riportoContoEconomico($db, $pklavoro, $utility, $negozi, $contoEconomico, $mesePrecedente, $anno, $dataGenerazioneSaldo, $descrizioneSaldo, $ggMese) {

        require_once 'menubanner.template.php';

        $config = $utility->getConfig();

        $conti = pg_fetch_all($contoEconomico);

        foreach ($conti as $conto) {

            foreach ($negozi as $negozio) {

                $replace = array(
                    '%datareg_da%' => '01/' . $mesePrecedente . '/' . $anno,
                    '%datareg_a%' => $ggMese[$mesePrecedente] . '/' . $mesePrecedente . '/' . $anno,
                    '%codnegozio%' => $negozio,
                    '%codconto%' => $conto['cod_conto'],
                    '%codsottoconto%' => $conto['cod_sottoconto']
                );

                $sqlTemplate = self::$root . $config['query'] . self::$querySaldoConto;

                $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
                $result = $db->execSql($sql);

                if (result) {
                    foreach (pg_fetch_all($result) as $row) {

                        /**
                         * Se il conto ha un totale movimenti = zero il saldo non viene riportato
                         */
                        if ($row['tot_conto'] != 0) {

                            /**
                             * tip_conto =  1 > Dare
                             * tip_conto = -1 > Avere
                             */
                            $dareAvere = ($row['tip_conto'] == 1) ? "D" : "A";
                            $this->inserisciSaldo($db, $utility, $negozio, $conto['cod_conto'], $conto['cod_sottoconto'], $dataGenerazioneSaldo, $descrizioneSaldo, abs($row['tot_conto']), $dareAvere);
                        }
                    }
                }
            }
        }
    }

}

?>
<?php

require_once 'saldi.abstract.class.php';
require_once 'utility.class.php';
require_once 'nexus6.main.interface.php';
require_once 'lavoroPianificato.class.php';
require_once 'conto.class.php';
require_once 'sottoconto.class.php';
require_once 'saldo.class.php';

/**
 * Questa classe è rieseguibile.
 * Se un saldo per un conto e una data c'è già in tabella viene aggiornato altrimenti viene inserito
 * @author stefano
 *
 */
class RiportoSaldoPeriodico extends SaldiAbstract implements MainNexus6Interface {

    public static $querySaldoConto = "/saldi/saldoConto.sql";

    function __construct() {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        
        $agent = parent::getInfoFromServer('HTTP_USER_AGENT');
        if (strpos($agent, 'Windows') === false) {
            self::$projectRoot = $array['linuxProjectRoot'];
        } else {
            self::$projectRoot = $array['windowsProjectRoot'];
        }
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::RIPORTO_SALDO) === NULL) {
            parent::setIndexSession(self::SALDO, serialize(new RiportoSaldoPeriodico()));
        }
        return unserialize(parent::getIndexSession(self::RIPORTO_SALDO));
    }

    public function start($db, $pklavoro, $project_root) {

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
         * Imposto la root del progetto
         */
        
        $root = (parent::isNotEmpty(self::$projectRoot) ? self::$projectRoot : $project_root);
        
        /**
         * Riporto stato patrimoniale
         */
                        
        if ($conto->leggiStatoPatrimoniale($db, $root)) {

            $this->riportoStatoPatrimoniale($db, $root, $lavoroPianificato, $utility, $negozi, $conto, $mesePrecedente, $anno, $dataGenerazioneSaldo, $descrizioneSaldo, $ggMese);

            $da = '01/' . $mesePrecedente . '/' . $anno;
            $a = $ggMese[$mesePrecedente] . '/' . $mesePrecedente . '/' . $anno;

            $riportoStatoPatrimoniale_Ok = TRUE;
        }

        /**
         * Riporto conto economico.
         * Il primo riporto dell'anno non viene fatto. I conti ripartono da zero.
         */
        if (date("m/d", strtotime($lavoroPianificato->getDatLavoro())) != "01/01") {

            if ($conto->leggiContoEconomico($db, $root)) {

                $this->riportoContoEconomico($db, $root, $lavoroPianificato, $utility, $negozi, $conto, $mesePrecedente, $anno, $dataGenerazioneSaldo, $descrizioneSaldo, $ggMese);

                $da = '01/' . $mesePrecedente . '/' . $anno;
                $a = $ggMese[$mesePrecedente] . '/' . $mesePrecedente . '/' . $anno;

                $riportoContoEconomico_Ok = TRUE;
            }
        }

        /**
         * Se uno dei due riporti è andato bene considero il lavoro eseguito
         */
        if (($riportoStatoPatrimoniale_Ok) or ( $riportoContoEconomico_Ok)) {
            $lavoroPianificato->setStaLavoro("10");
            $lavoroPianificato->cambioStato($db);
            return TRUE;
        } else
            return FALSE;
    }

    private function riportoStatoPatrimoniale($db, $root, $lavoroPianificato, $utility, $negozi, $conto, $mesePrecedente, $anno, $dataGenerazioneSaldo, $descrizioneSaldo, $ggMese) {

        $config = $utility->getConfig();
        $saldo = Saldo::getInstance();

        $saldo->setRoot($root);

        /**
         * Tutti i conti dello Stato Patrimoniale
         */
        foreach ($conto->getContiStatoPatrimoniale() as $conto) {

            /**
             * Per ciascun conto effettuo la totalizzazione delle registrazioni per ciascun negozio
             */
//            $dareAvere_conto = ($conto[Conto::TIP_CONTO] = "Avere") ? "A" : "D";  // prelevo il tipo del conto Dare/Avere

            foreach ($negozi as $negozio) {

                $saldo->setDataregDA('01/' . $mesePrecedente . '/' . $anno);
                $saldo->setDataregA($ggMese[$mesePrecedente] . '/' . $mesePrecedente . '/' . $anno);
                $saldo->setCodNegozio($negozio);
                $saldo->setCodConto($conto[Conto::COD_CONTO]);
                $saldo->setCodSottoconto($conto[Sottoconto::COD_SOTTOCONTO]);
                parent::setIndexSession(self::SALDO, serialize($saldo));

                $result = $saldo->leggiSaldoConto($db, $root);
                $numTot = pg_num_rows($result);

                if ($numTot > 0) {

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
                    parent::setIndexSession(self::SALDO, serialize($saldo));
                    
                    $this->gestioneSaldo($db);
                }
            }
        }
    }

    private function riportoContoEconomico($db, $root, $lavoroPianificato, $utility, $negozi, $conto, $mesePrecedente, $anno, $dataGenerazioneSaldo, $descrizioneSaldo, $ggMese) {

        $config = $utility->getConfig();
        $saldo = Saldo::getInstance();

        $saldo->setRoot($root);
        
        foreach ($conto->getContiContoEconomico() as $conto) {

            foreach ($negozi as $negozio) {

                $saldo->setDataregDA('01/' . $mesePrecedente . '/' . $anno);
                $saldo->setDataregA($ggMese[$mesePrecedente] . '/' . $mesePrecedente . '/' . $anno);
                $saldo->setCodNegozio($negozio);
                $saldo->setCodConto($conto[Conto::COD_CONTO]);
                $saldo->setCodSottoconto($conto[Sottoconto::COD_SOTTOCONTO]);
                parent::setIndexSession(self::SALDO, serialize($saldo));

                $result = $saldo->leggiSaldoConto($db, $root);
                $numTot = pg_num_rows($result);

                if ($numTot > 0) {

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
                     * Se il conto ha un totale movimenti = zero il saldo non viene riportato
                     */
                    if ($totale_conto != 0) {

                        /**
                         * tip_conto =  1 > Dare
                         * tip_conto = -1 > Avere
                         */
                        $dareAvere = ($row[Conto::TIP_CONTO] == 1) ? "D" : "A";

                        $saldo->setDatSaldo($dataGenerazioneSaldo);
                        $saldo->setDesSaldo($descrizioneSaldo);
                        $saldo->setImpSaldo(abs($totale_conto));
                        $saldo->setIndDareavere($dareAvere);
                        parent::setIndexSession(self::SALDO, serialize($saldo));

                        $this->gestioneSaldo($db);
                    }
                }
            }
        }
    }

}

?>
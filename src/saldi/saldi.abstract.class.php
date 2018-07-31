<?php

require_once 'nexus6.abstract.class.php';
require_once 'saldo.class.php';

abstract class SaldiAbstract extends Nexus6Abstract {

    private static $_instance = null;
    public static $messaggio;

    /*
     * Query ---------------------------------------------------------------
     */
    public static $queryCreaLavoroPianificato = "/main/creaLavoroPianificato.sql";

    /*
     * Getters e Setters ---------------------------------------------------
     */

    public function setMessaggio($messaggio) {
        self::$messaggio = $messaggio;
    }

    public function getMessaggio() {
        return self::$messaggio;
    }

    /**
     * Metodi comuni di utilita della prima note
     */

    /**
     * Se il saldo c'è già sulla tabella viene aggiornato altrimenti viene inserito
     * @param unknown $db
     * @param unknown $utility
     * @param unknown $codnegozio
     * @param unknown $codconto
     * @param unknown $codsottoconto
     * @param unknown $datsaldo
     * @param unknown $dessaldo
     * @param unknown $impsaldo
     * @param unknown $inddareavere
     * @return unknown
     */
    public function inserisciSaldo($db, $utility, $codnegozio, $codconto, $codsottoconto, $datsaldo, $dessaldo, $impsaldo, $inddareavere) {

        $saldo = Saldo::getInstance();

        if ($saldo->leggiSaldo($db)) {

            /**
             * Se il saldo calcolato è significativo, aggiorno il saldo del conto
             * altrimenti elimino il saldo del conto
             */
            if ($saldo->getImpSaldp() != 0) {
                $saldo->aggiornaSaldo($db);
            } else {
                $saldo->cancellaSaldo($db);
            }
        } else {

            /**
             * Se il saldo calcolato è significativo, creo il saldo del conto
             */
            if ($impsaldo != 0) {
                $saldo->creaSaldo($db);
            }
        }
    }

    public function caricaDateRiportoSaldo($db) {

        $saldo = Saldo::getInstance();
        $saldo->caricaDateRiporto($db);

        $elencoDateRiportoSaldi = "";

        foreach ($saldo->getDateRiportoSaldi() as $row) {

            if ($row[Saldo::DAT_SALDO] == $saldo->getDatSaldoSel()) {
                $elencoDateRiportoSaldi .= "<option value='" . $row[Saldo::DAT_SALDO] . "' selected >" . date("d/m/Y", strtotime($row[Saldo::DAT_SALDO])) . "</option>";
            } else {
                $elencoDateRiportoSaldi .= "<option value='" . $row[Saldo::DAT_SALDO] . "'>" . date("d/m/Y", strtotime($row[Saldo::DAT_SALDO])) . "</option>";
            }
        }
        return $elencoDateRiportoSaldi;
    }

    public function caricaTuttiConti($db) {

        $conto = Conto::getInstance();
        $conto->leggiTuttiConti($db);

        foreach ($conto->getConti() as $row) {

            $conto = $row[Conto::COD_CONTO] . '-' . $row[Sottoconto::COD_SOTTOCONTO];
            $desConto = $row[Conto::DES_CONTO] . ' - ' . $row[Sottoconto::DES_SOTTOCONTO];

            if ($conto == $conto->getCodContoSel()) {
                $elenco_conti .= "<option value='" . $conto . "' selected >" . $conto . ' : ' . $desConto . "</option>";
            } else {
                $elenco_conti .= "<option value='" . $conto . "'>" . $conto . ' : ' . $desConto . "</option>";
            }
        }
        return $elenco_conti;
    }

    public function inserisciLavoroPianificato($db, $utility, $dat_lavoro, $des_lavoro, $fil_esecuzione_lavoro, $cla_esecuzione_lavoro, $sta_lavoro) {

        $array = $utility->getConfig();
        $replace = array(
            '%dat_lavoro%' => $dat_lavoro,
            '%des_lavoro%' => $des_lavoro,
            '%fil_esecuzione_lavoro%' => $fil_esecuzione_lavoro,
            '%cla_esecuzione_lavoro%' => $cla_esecuzione_lavoro,
            '%sta_lavoro%' => $sta_lavoro
        );
        $sqlTemplate = self::$root . $array['query'] . self::$queryCreaLavoroPianificato;

        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);
        return $result;
    }

}

?>

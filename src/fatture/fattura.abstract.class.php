<?php

require_once 'nexus6.abstract.class.php';

abstract class FatturaAbstract extends Nexus6Abstract {

    public static $messaggio;
    // Query ---------------------------------------------------------------

    public static $queryRicercaClienti = "/fatture/ricercaClienti.sql";
    public static $queryRicercaNumeroFattura = "/fatture/ricercaNumeroFattura.sql";
    public static $queryRicercaDatiCliente = "/fatture/ricercaDatiCliente.sql";

    // Getters e Setters ---------------------------------------------------

    public function setMessaggio($messaggio) {
        self::$messaggio = $messaggio;
    }

    // ------------------------------------------------

    public function getMessaggio() {
        return self::$messaggio;
    }

    // Metodi comuni di utilita della prima note ---------------------------

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

        $sqlTemplate = self::$root . $array['query'] . self::$queryRicercaClienti;
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

    /**
     * Questo metodo preleva l'ultimo progressivo fattura utilizzato
     *
     * @param unknown $utility
     * @param unknown $db
     * @param unknown $categoriaCliente
     * @param unknown $codiceNegozio
     * @return un progressivo fattura utilizzabile
     */
    public function caricaNumeroFattura($utility, $db, $categoriaCliente, $codiceNegozio) {

        $array = $utility->getConfig();
        $replace = array(
            '%cat_cliente%' => trim($categoriaCliente),
            '%neg_progr%' => trim($codiceNegozio)
        );

        $sqlTemplate = self::$root . $array['query'] . self::$queryRicercaNumeroFattura;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        foreach (pg_fetch_all($result) as $row) {
            $numeroFattura = $row['num_fattura_ultimo'];
            $_SESSION["nota_testa_fattura"] = $row["nota_testa_fattura"];
            $_SESSION["nota_piede_fattura"] = $row["nota_piede_fattura"];
        }
        return $numeroFattura;
    }

    /**
     * Questo metodo preleva il tipo addebito di un cliente
     *
     * @param unknown $utility
     * @param unknown $db
     * @param unknown $idcliente
     */
    public function caricaTipoAddebitoCliente($utility, $db, $idcliente) {

        $tipoAddebito = "";
        $array = $utility->getConfig();
        $replace = array(
            '%id_cliente%' => trim($idcliente),
        );

        $sqlTemplate = self::$root . $array['query'] . self::$queryRicercaDatiCliente;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            foreach (pg_fetch_all($result) as $row) {
                $tipoAddebito = trim($row['tip_addebito']);

                $_SESSION["descliente"] = trim($row['des_cliente']);
                $_SESSION["indirizzocliente"] = trim($row['des_indirizzo_cliente']);
                $_SESSION["cittacliente"] = trim($row['des_citta_cliente']);
                $_SESSION["capcliente"] = trim($row['cap_cliente']);
                $_SESSION["pivacliente"] = trim($row['cod_piva']);
                $_SESSION["cfiscliente"] = trim($row['cod_fisc']);
            }
        }
        return $tipoAddebito;
    }

    public function intestazione($fatturaAziendaConsortile) {

        $fatturaAziendaConsortile->setTitle(iconv('UTF-8', 'windows-1252', "Cooperativa Chopin - Cooperativa sociale - ONLUS"));
        $fatturaAziendaConsortile->setTitle1(iconv('UTF-8', 'windows-1252', "Diversamente Impresa: Esperienza occupazionale-lavorativa"));
        $fatturaAziendaConsortile->setTitle2(iconv('UTF-8', 'windows-1252', "Domicilio fiscale: via San Martirio, 1 - 24030 Villa d'Adda (BG) - C.F./P.IVA: 03691430163"));
        return $fatturaAziendaConsortile;
    }

    public function sezioneNotaPiede($fatturaAziendaConsortile, $fattura) {

        if (parent::isNotEmpty($fattura->getNotaPiede())) {
            $nota = explode("\\", $fattura->getNotaPiede());
        }
        $fatturaAziendaConsortile->aggiungiLineaNota($nota, 12, 242);
        return $fatturaAziendaConsortile;
    }

    public function sezionePagamento($fatturaAziendaConsortile, $fattura) {
        $fatturaAziendaConsortile->AddPage();
        $fatturaAziendaConsortile->pagamento($fattura->getTipAddebito());
        return $fatturaAziendaConsortile;
    }

    public function sezioneBanca($fatturaAziendaConsortile, $fattura) {
        $fatturaAziendaConsortile->banca($fattura->getDesRagsocBanca(), $fattura->getCodIbanBanca());
        return $fatturaAziendaConsortile;
    }

    public function sezioneDestinatario($fatturaAziendaConsortile, $fattura) {
        $fatturaAziendaConsortile->destinatario($fattura->getDesCliente(), $fattura->getIndCliente(), $fattura->getCittaCliente(), $fattura->getCapCliente(), $fattura->getPivaCliente(), $fattura->getCfiscCliente());
        return $fatturaAziendaConsortile;
    }

}

?>
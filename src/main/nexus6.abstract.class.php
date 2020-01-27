<?php

require_once 'lavoroPianificato.class.php';

abstract class Nexus6Abstract implements MainNexus6Interface {

    public $root;
    public $testata;
    public $piede;
    public $messaggioInfo;
    public $messaggioErrore;
    public $azione;
    public $testoAzione;
    public $titoloPagina;
    public $confermaTip;
    public static $replace;
    public $menu;
    public $elenco_causali;
    public $elenco_fornitori;
    public $elenco_clienti;
    public $elenco_conti;
    public $elenco_mercati;
    public $errorStyle = "border-color:#ff0000; border-width:1px;";

    /*
     * Query ------------------------------------------------------------------------------
     */
    public static $queryControllaScadenzeFornitoreSuperate = "/main/controllaScadenzeFornitoreSuperate.sql";
    public static $queryControllaScadenzeClienteSuperate = "/main/controllaScadenzeClienteSuperate.sql";
    public static $queryControllaRegistrazioniInErrore = "/main/controllaRegistrazioniInErrore.sql";

    /*
     * Costanti
     */
    
    const MODO = "modo";
    const START = "start";
    const GO = "go";
    
    const DATA_REGISTRAZIONE_DA_RICERCA = "datareg_da";
    const DATA_REGISTRAZIONE_A_RICERCA = "datareg_a";
    const CODICE_NEGOZIO_RICERCA = "codneg_sel";
    const CAUSALE_RICERCA = "causale";
    const DATA_REGISTRAZIONE = "datareg";
    
    const CATEGORIA_CONTO = "catconto";
    const DES_CONTO = "desconto";
    const DES_SOTTOCONTO = "dessottoconto";
    
    const IMPORTO_DETTAGLIO = "importo";
    const CODICE_CONTO_DETTAGLIO = "codconto";
    const CODICE_SOTTOCONTO_DETTAGLIO = "codsottoconto";
    const DES_SOTTOCONTO_DETTAGLIO = "dessottoconto";
    const DARE_AVERE_DETTAGLIO = "dareAvere";
    const ID_DETTAGLIO = "iddettaglio";    
    const IMPORTO_DETTAGLIO_IN_SCADENZA = "importo_dettaglio";
    const CATEGORIA_CONTO_RICERCA = "catconto_sel";
    const TIPO_CONTO_RICERCA = "tipconto_sel";
    const CODICE_CONTO_RICERCA = "codconto_sel";
    const CODICE_CONTO_NUOVO_MODIFICA = "conto_sel_nuovo";
    const CODICE_CONTO_CASSA = "contocassa";
    
    const CODICE_CONTO_CREAZIONE = "codconto_cre";
    const DES_CONTO_CREAZIONE = "desconto_cre";
    const CATEGORIA_CONTO_CREAZIONE = "catconto_cre";
    const INDICATORE_DARE_AVERE_CREAZIONE = "dareavere_cre";
    const INDICATORE_PRESENZA_IN_BILANCIO_CREAZIONE = "indpresenza_cre";
    const INDICATORE_VISUALIZZAZIONE_SOTTOCONTI_CREAZIONE = "indvissottoconti_cre";
    const NUMERO_RIGA_BILANCIO_CREAZIONE = "numrigabilancio_cre";
    
    const CODICE_CONTO_MODIFICA = "codconto_mod";
    const DES_CONTO_MODIFICA = "desconto_mod";
    const CATEGORIA_CONTO_MODIFICA = "catconto_mod";
    const INDICATORE_DARE_AVERE_MODIFICA = "dareavere_mod";
    const INDICATORE_PRESENZA_IN_BILANCIO_MODIFICA = "indpresenza_mod";
    const INDICATORE_VISUALIZZAZIONE_SOTTOCONTI_MODIFICA = "indvissottoconti_mod";
    const NUMERO_RIGA_BILANCIO_MODIFICA = "numrigabilancio_mod";
    
    const CODICE_CONTO_GRUPPO_MODIFICA = "codconto_modgru";
    const CODICE_SOTTOCONTO_GRUPPO_MODIFICA = "codsottoconto_modgru";
    const INDICATORE_GRUPPO_MODIFICA = "indgruppo_modgru";
    
    const CODICE_SOTTOCONTO_GRUPPO_CREAZIONE = "codsottoconto_new";
    const DES_SOTTOCONTO_GRUPPO_CREAZIONE = "dessottoconto_new";
    const INDICATORE_GRUPPO_CREAZIONE = "indgruppo_new";
    
    const CODICE_SOTTOCONTO_GRUPPO_CANCELLAZIONE = "codsottoconto_del";
    const CODICE_CONTO_GRUPPO_CANCELLAZIONE = "codconto_del";
    
    const CODICE_CONTO_RICERCA_MOVIMENTI = "ccon_mov";
    const CODICE_SOTTOCONTO_RICERCA_MOVIMENTI = "csot_mov";
    const DATA_REGISTRAZIONE_DA_RICERCA_MOVIMENTI = "dtda_mov";
    const DATA_REGISTRAZIONE_A_RICERCA_MOVIMENTI = "dta_mov";
    const CODICE_NEGOZIO_RICERCA_MOVIMENTI = "cneg_mov";
    const SALDI_INCLUSI_RICERCA_MOVIMENTI = "sal_mov";
   
    const DATA_REGISTRAZIONE_CREAZIONE = "datareg_cre";
    const DES_REGISTRAZIONE_CREAZIONE = "descreg_cre";
    const CAUSALE_REGISTRAZIONE_CREAZIONE = "causale_cre";
    const CODICE_NEGOZIO_REGISTRAZIONE_CREAZIONE = "codneg_cre";
    const FORNITORE_REGISTRAZIONE_CREAZIONE = "fornitore_cre";
    const CLIENTE_REGISTRAZIONE_CREAZIONE = "cliente_cre";
    const NUMERO_FATTURA_REGISTRAZIONE_CREZIONE = "numfatt_cre";
    const REGISTRAZIONE_APERTA = "00";
    const REGISTRAZIONE_ERRATA = "02";    
    const TABELLA_SCADENZE_APERTE_CREAZIONE = "scadenzesuppl_cre";
    
    const DATA_REGISTRAZIONE_MODIFICA = "datareg_mod";
    const DES_REGISTRAZIONE_MODIFICA = "descreg_mod";
    const CAUSALE_REGISTRAZIONE_MODIFICA = "causale_mod";
    const CODICE_NEGOZIO_REGISTRAZIONE_MODIFICA = "codneg_mod";
    const FORNITORE_REGISTRAZIONE_MODIFICA = "fornitore_mod";
    const CLIENTE_REGISTRAZIONE_MODIFICA = "cliente_mod";
    const NUMERO_FATTURA_REGISTRAZIONE_MODIFICA = "numfatt_mod";
    const NUMERO_FATTURA_REGISTRAZIONE_ORIGINALE_MODIFICA = "numfatt_mod_orig";
    const TABELLA_SCADENZE_APERTE_MODIFICA = "scadenzesuppl_mod";
    const NOTA_TESTATA_MODIFICA = "notatesta_mod";
    const NOTA_PIEDE_MODIFICA = "notapiede_mod";
    
    const CODICE_CONTO_DETTAGLIO_CREAZIONE   = "newcontodett_cre";
    const IMPORTO_DETTAGLIO_CREAZIONE   = "newimpdett_cre";
    const SEGNO_DETTAGLIO_CREAZIONE   = "newsegnodett_cre";
    
    const TABELLA_SCADENZE = "scadenzeTable";
    
    const CODICE_CLIENTE_INCASSO_CREAZIONE = "cliente_inc_cre";
//    const CODICE_NEGOZIO_INCASSO_CREAZIONE = "codneg_inc_cre";
    const CODICE_NEGOZIO_INCASSO_CREAZIONE = "codnegozio_inc_cre";
    
    const TABELLA_SCADENZE_APERTE_INCASSO_CREAZIONE = "scadenze_aperte_inc_cre";
    const TABELLA_SCADENZE_CHIUSE_INCASSO_CREAZIONE = "scadenze_chiuse_inc_cre";
    const TABELLA_DETTAGLI_INCASSO_CREAZIONE = "dettagli_inc_cre";
    const DATA_REGISTRAZIONE_INCASSO_CREAZIONE = "datareg_inc_cre";
    const DES_REGISTRAZIONE_INCASSO_CREAZIONE = "descreg_inc_cre";
    const CODICE_CAUSALE_INCASSO_CREAZIONE = "causale_inc_cre";
    const NUMERO_FATTURA_INCASSO_CREAZIONE = "numfatt_inc_cre";
        
    const CODICE_CLIENTE_INCASSO_MODIFICA = "cliente_inc_mod";
    const CODICE_NEGOZIO_INCASSO_MODIFICA = "codneg_inc_mod";
    const TABELLA_SCADENZE_APERTE_INCASSO_MODIFICA = "scadenze_aperte_inc_mod";
    const TABELLA_SCADENZE_CHIUSE_INCASSO_MODIFICA = "scadenze_chiuse_inc_mod";
    const TABELLA_DETTAGLI_INCASSO_MODIFICA = "dettagli_inc_mod";
    const DATA_REGISTRAZIONE_INCASSO_MODIFICA = "datareg_inc_mod";
    const DES_REGISTRAZIONE_INCASSO_MODIFICA = "descreg_inc_mod";
    const CODICE_CAUSALE_INCASSO_MODIFICA = "causale_inc_mod";
    const NUMERO_FATTURA_INCASSO_MODIFICA = "numfatt_inc_mod";    
    
    const NUMERO_FATTURA_FORNITORE = "numfatt";
    const DATA_SCADENZA_FORNITORE = "datascad_for";
    const DATA_SCADENZA_NUOVA = "datascad_new";
    const IMPORTO_SCADENZA_FORNITORE = "impscad_for";

    const DATA_SCADENZA_CLIENTE = "datascad_cli";
    const IMPORTO_SCADENZA_CLIENTE = "impscad_cli";
    
    const DATA_SCADENZA_NUOVA_FORNITORE = "datascad_new_for";
    const DATA_SCADENZA_VECCHIA_FORNITORE = "datascad_old_for";
    
    const DATA_SCADENZA_NUOVA_CLIENTE = "datascad_new_cli";
    const DATA_SCADENZA_VECCHIA_CLIENTE = "datascad_old_cli";

    const SCADENZE_APERTE_INCASSO_MODIFICA = "scadenze_aperte_inc_mod";
    const SCADENZE_CHIUSE_INCASSO_MODIFICA = "scadenze_chiuse_inc_mod";
    const DETTAGLIO_INCASSO_MODIFICA = "dettagli_inc_mod";

    const ID_SCADENZA_CLIENTE = "idscadcli";
    const TABELLA_SCADENZE_APERTE = "idtableaperte";
    const TABELLA_SCADENZE_CHIUSE = "idtablechiuse";

    const DATA_REGISTRAZIONE_PAGAMENTO_CREAZIONE = "datareg_pag_cre";
    const DES_REGISTRAZIONE_PAGAMENTO_CREAZIONE = "descreg_pag_cre";
    const CODICE_CAUSALE_PAGAMENTO_CREAZIONE = "causale_pag_cre";
    const FORNITORE_PAGAMENTO_CREAZIONE = "fornitore_pag_cre";
    const CODICE_NEGOZIO_PAGAMENTO_CREAZIONE = "codnegozio_pag_cre";
    const NUMERO_FATTURA_PAGAMENTO_CREAZIONE = "numfatt_pag_cre";
    const TABELLA_SCADENZE_APERTE_PAGAMENTO_CREAZIONE = "scadenze_aperte_pag_cre";
    const TABELLA_SCADENZE_CHIUSE_PAGAMENTO_CREAZIONE = "scadenze_chiuse_pag_cre";
    const DETTAGLI_PAGAMENTO_CREAZIONE = "dettagli_pag_cre";
    
    const DATA_REGISTRAZIONE_PAGAMENTO_MODIFICA = "datareg_pag_mod";
    const DES_REGISTRAZIONE_PAGAMENTO_MODIFICA = "descreg_pag_mod";
    const CODICE_CAUSALE_PAGAMENTO_MODIFICA = "causale_pag_mod";
    const CODICE_NEGOZIO_PAGAMENTO_MODIFICA = "codneg_pag_mod";
    const FORNITORE_PAGAMENTO_MODIFICA = "fornitore_pag_mod";
    const NUMERO_FATTURA_PAGAMENTO_MODIFICA = "numfatt_pag_mod";
    const TABELLA_SCADENZE_APERTE_PAGAMENTO_MODIFICA = "scadenze_aperte_pag_mod";
    const TABELLA_SCADENZE_CHIUSE_PAGAMENTO_MODIFICA = "scadenze_chiuse_pag_mod";
    const DETTAGLI_PAGAMENTO_MODIFICA = "dettagli_pag_mod";
    
    const ID_SCADENZA = "idscad";
    const ID_SCADENZA_FORNITORE = "idscadfor";
    
    const DATA_REGISTRAZIONE_CORRISPETTIVO_MERCATO_CREAZIONE = "datareg_cormer_cre";
    const DES_CORRISPETTIVO_MERCATO_CREAZIONE = "descreg_cormer_cre";
    const CAUSALE_CORRISPETTIVO_MERCATO_CREAZIONE = "causale_cormer_cre";
    const MERCATO_CORRISPETTIVO_MERCATO_CREAZIONE = "mercato_cormer_cre";
    const CODICE_NEGOZIO_CORRISPETTIVO_MERCATO_CREAZIONE = "codneg_cormer_cre";
    const CODICE_CONTO_CORRISPETTIVO_MERCATO_CREAZIONE = "codconto_cormer_cre";
    const IMPORTO_CORRISPETTIVO_MERCATO_CREAZIONE = "importo_cormer_cre";
    const ALIQUOTA_CORRISPETTIVO_MERCATO_CREAZIONE = "aliquota_cormer_cre";
    const IVA_CORRISPETTIVO_MERCATO_CREAZIONE = "iva_cormer_cre";
    const IMPONIBILE_CORRISPETTIVO_MERCATO_CREAZIONE = "imponibile_cormer_cre";
    
    const DATA_REGISTRAZIONE_CORRISPETTIVO_MERCATO_MODIFICA = "datareg_cormer_mod";
    const DES_CORRISPETTIVO_MERCATO_MODIFICA = "descreg_cormer_mod";
    const CAUSALE_CORRISPETTIVO_MERCATO_MODIFICA = "causale_cormer_mod";
    const CODICE_NEGOZIO_CORRISPETTIVO_MERCATO_MODIFICA = "codneg_cormer_mod";
    const CODICE_CONTO_CORRISPETTIVO_MERCATO_MODIFICA = "codconto_cormer_mod";
    const IMPORTO_CORRISPETTIVO_MERCATO_MODIFICA = "importo_cormer_mod";
    const ALIQUOTA_CORRISPETTIVO_MERCATO_MODIFICA = "aliquota_cormer_mod";
    const IVA_CORRISPETTIVO_MERCATO_MODIFICA = "iva_cormer_mod";
    const IMPONIBILE_CORRISPETTIVO_MERCATO_MODIFICA = "imponibile_cormer_mod";
    const MERCATO_CORRISPETTIVO_MERCATO_MODIFICA = "mercato_cormer_mod";
    
    const DATA_REGISTRAZIONE_CORRISPETTIVO_NEGOZIO_CREAZIONE = "datareg_corneg_cre";
    const DES_CORRISPETTIVO_NEGOZIO_CREAZIONE = "descreg_corneg_cre";
    const CAUSALE_CORRISPETTIVO_NEGOZIO_CREAZIONE = "causale_corneg_cre";
    const MERCATO_CORRISPETTIVO_NEGOZIO_CREAZIONE = "mercato_corneg_cre";
    const CODICE_NEGOZIO_CORRISPETTIVO_NEGOZIO_CREAZIONE = "codneg_corneg_cre";
    const CODICE_CONTO_CORRISPETTIVO_NEGOZIO_CREAZIONE = "codconto_corneg_cre";
    const IMPORTO_CORRISPETTIVO_NEGOZIO_CREAZIONE = "importo_corneg_cre";
    const ALIQUOTA_CORRISPETTIVO_NEGOZIO_CREAZIONE = "aliquota_corneg_cre";
    const IVA_CORRISPETTIVO_NEGOZIO_CREAZIONE = "iva_corneg_cre";
    const IMPONIBILE_CORRISPETTIVO_NEGOZIO_CREAZIONE = "imponibile_corneg_cre";
    
    const DATA_REGISTRAZIONE_CORRISPETTIVO_NEGOZIO_MODIFICA = "datareg_corneg_mod";
    const DES_CORRISPETTIVO_NEGOZIO_MODIFICA = "descreg_corneg_mod";
    const CAUSALE_CORRISPETTIVO_NEGOZIO_MODIFICA = "causale_corneg_mod";
    const CODICE_NEGOZIO_CORRISPETTIVO_NEGOZIO_MODIFICA = "codneg_corneg_mod";
    const CODICE_CONTO_CORRISPETTIVO_NEGOZIO_MODIFICA = "codconto_corneg_mod";
    const IMPORTO_CORRISPETTIVO_NEGOZIO_MODIFICA = "importo_corneg_mod";
    const ALIQUOTA_CORRISPETTIVO_NEGOZIO_MODIFICA = "aliquota_corneg_mod";
    const IVA_CORRISPETTIVO_NEGOZIO_MODIFICA = "iva_corneg_mod";
    const IMPONIBILE_CORRISPETTIVO_NEGOZIO_MODIFICA = "imponibile_corneg_mod";
    const MERCATO_CORRISPETTIVO_NEGOZIO_MODIFICA = "mercato_corneg_mod";
    
    const CODICE_FORNITORE_CREAZIONE = "codforn_cre";
    const DES_FORNITORE_CREAZIONE = "desforn_cre";
    const INDIRIZZO_FORNITORE_CREAZIONE = "indforn_cre";
    const CITTA_FORNITORE_CREAZIONE = "cittaforn_cre";
    const CAP_FORNITORE_CREAZIONE = "capforn_cre";
    const TIPO_ADDEBITO_CREAZIONE = "tipoadd_cre";
    const GIORNI_SCADENZA_FATTURA_CREAZIONE = "ggscadfat_cre";
    
    const CODICE_FORNITORE_MODIFICA = "codforn_mod";
    const DES_FORNITORE_MODIFICA = "desforn_mod";
    const INDIRIZZO_FORNITORE_MODIFICA = "indforn_mod";
    const CITTA_FORNITORE_MODIFICA = "cittaforn_mod";
    const CAP_FORNITORE_MODIFICA = "capforn_mod";
    const TIPO_ADDEBITO_MODIFICA = "tipoadd_mod";
    const GIORNI_SCADENZA_FATTURA_MODIFICA = "ggscadfat_mod";
    
    const CODICE_CLIENTE_CREAZIONE = "codcli_cre";
    const CATEGORIA_CLIENTE_CREAZIONE = "catcli_cre";
    const DES_CLIENTE_CREAZIONE= "descli_cre";
    const INDIRIZZO_CLIENTE_CREAZIONE = "indcli_cre";
    const CITTA_CLIENTE_CREAZIONE = "cittacli_cre";
    const CAP_CLIENTE_CREAZIONE = "capcli_cre";
    const TIPO_ADDEBITO_CLIENTE_CREAZIONE = "tipoadd_cre";
    const PARTITA_IVA_CLIENTE_CREAZIONE = "pivacli_cre";
    const CODICE_FISCALE_CLIENTE_CREAZIONE = "cfiscli_cre";
    
    const CODICE_CLIENTE_MODIFICA = "codcli_mod";
    const CATEGORIA_CLIENTE_MODIFICA = "catcli_mod";
    const DES_CLIENTE_MODIFICA = "descli_mod";
    const INDIRIZZO_CLIENTE_MODIFICA = "indcli_mod";
    const CITTA_CLIENTE_MODIFICA = "cittacli_mod";
    const CAP_CLIENTE_MODIFICA = "capcli_mod";
    const TIPO_ADDEBITO_CLIENTE_MODIFICA = "tipoadd_mod";
    const PARTITA_IVA_CLIENTE_MODIFICA = "pivacli_mod";
    const CODICE_FISCALE_CLIENTE_MODIFICA = "cfiscli_mod";
    
    const CODICE_MERCATO_CREAZIONE = "codmer_cre";
    const DES_MERCATO_CREAZIONE = "desmer_cre";
    const CITTA_MERCATO_CREAZIONE = "citmer_cre";
    const NEGOZIO_MERCATO_CREAZIONE = "negmer_cre";
    
    const CODICE_MERCATO_MODIFICA = "codmer_mod";
    const DES_MERCATO_MODIFICA = "desmer_mod";
    const CITTA_MERCATO_MODIFICA = "citmer_mod";
    const NEGOZIO_MERCATO_MODIFICA = "negmer_mod";
    
    const CODICE_FISCALE = "codfisc";
    const PARTITA_IVA = "codpiva";
    const DES_CLIENTE = "descliente";
    const CODICE_CAUSALE = "codCausale";
    
    const ID_CLIENTE = "idcliente";
    const ID_FORNITORE = "idfornitore";
    const ID_MERCATO = "idmercato";
    const ID_REGISTRAZIONE = "idreg";
    const ID_INCASSO = "idinc";
    const ID_PAGAMENTO = "idpag";
    const ID_PAGM = "idPagamento";
    const ID_SCAD = "idScadenza";
    const ID_SCAD_CLIENTE = "idScadenzaCliente";
    
    const DATA_SCADENZA_MODIFICA = "datascad_mod";
    const NOTA_SCADENZA_MODIFICA = "notascad_mod";
    const CODICE_NEGOZIO_SCADENZA_MODIFICA = "negozio_mod";
    const IMPORTO_SCADENZA_MODIFICA = "impscad_mod";
    const NUMERO_FATTURA_SCADENZA_MODIFICA = "fatscad_mod";
    const NUMERO_FATTURA_SCADENZA_ORIGINALE_MODIFICA = "fatscad_orig_mod";
    const FORNITORE_SCADENZA_ORIGINALE_MODIFICA = "fornitore_orig_mod";

    const DATA_SCADENZA_CLIENTE_MODIFICA = "datascad_cli_mod";
    const NOTA_SCADENZA_CLIENTE_MODIFICA = "notascad_cli_mod";
    const CODICE_NEGOZIO_SCADENZA_CLIENTE_MODIFICA = "negozio_cli_mod";
    const IMPORTO_SCADENZA_CLIENTE_MODIFICA = "impscad_cli_mod";
    const NUMERO_FATTURA_SCADENZA_CLIENTE_MODIFICA = "fatscad_cli_mod";
    const NUMERO_FATTURA_SCADENZA_CLIENTE_ORIGINALE_MODIFICA = "fatscad_orig_cli_mod";
    const FORNITORE_SCADENZA_CLIENTE_ORIGINALE_MODIFICA = "fornitore_orig_cli_mod";
    
    const CODICE_CAUSALE_CREAZIONE = "codcausale_cre";
    const DES_CAUSALE_CREAZIONE = "descausale_cre";
    const CATEGORIA_CAUSALE_CREAZIONE = "catcausale_cre";
    
    const CODICE_CAUSALE_CONFIGURAZIONE = "codcausale_conf";
    
    const CODICE_CONTO_CONFIGURAZIONE = "codconto_conf";
    
    const CODICE_CAUSALE_MODIFICA = "codcausale_mod";
    const DES_CAUSALE_MODIFICA = "descausale_mod";
    const CATEGORIA_CAISALE_MODIFICA = "catcausale_mod";
    
    const CODICE_CAUSALE_CANCELLAZIONE = "codcausale_del";
    
    const CAT_CLIENTE_MODIFICA = "catcliente_mod";
    const CODICE_NEGOZIO_MODIFICA = "codnegozio_mod";

    const DATA_FATTURA = "datafat";
    const MESE_RIFERIMENTO = "meserif";
    const TITOLO = "titolo";
    const RAGIONE_SOCIALE_CLIENTE = "cliente";
    const TIPO_ADDEBITO = "tipoadd";
    const CODICE_NEGOZIO = "codneg";
    const CODICE_NEGOZIO_PRESENZE = "codnegpres";
    const NUMERO_FATTURA = "numfat";
    const RAGIONE_SOCIALE_BANCA_APPOGGIO = "ragsocbanca";
    const IBAN_BANCA_APPOGGIO = "ibanbanca";
    const TIPO_FATTURA = "tipofat";
    const NOME_COGNOME_ASSISTITO = "assistito";
    
    const CATEGORIA_CLIENTE = "catcliente";
    const QUANTITA_ARTICOLO = "quantita";
    const CODICE_ARTICOLO = "articolo";
    const IMPORTO_UNITARIO = "importo";
    const ALIQUOTA_IVA = "aliquota";
    const TOTALE_FATTURA = "totale";
    const IMPONIBILE_FATTURA = "imponibile";
    const IVA_FATTURA = "iva";
    const ID_ARTICOLO = "idarticolo";
    
    const ANNO_ESERCIZIO_RICERCA = "anno_eserczio_sel";
    const INDICATORE_SOLO_CONTO_ECONOMICO = "S";
    const INDICATORE_TUTTI_I_CONTI = "N";
    const INDICATORE_SALDI_INCLUSI = "saldiInclusi";
    const INDICATORE_CONTO_ECONOMICO = "soloContoEconomico";

    const DATA_SCADENZA_DA_RICERCA = "datascad_da";
    const DATA_SCADENZA_A_RICERCA = "datascad_a";
    const STATO_SCADENZA_RICERCA = "statoscad_sel";

    const MESE = "mese";
    const MESE_PRESENZE = "mesepres";
    const ANNO = "anno";
    const ANNO_PRESENZE = "annopres";
    const FILE = "file";
    const FILE_PRESENZE = "filepres";
    const DATA_IMPORTAZIONE_DA = "datada";
    const DATA_IMPORTAZIONE_A = "dataa";

    public static $mese = array(
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
    
    // Setters -----------------------------------------------------------------------------

    public function setTestata($testata) {
        self::$testata = $testata;
    }

    public function setPiede($piede) {
        self::$piede = $piede;
    }

    public function setAzione($azione) {
        self::$azione = $azione;
    }

    public function setTestoAzione($testoAzione) {
        self::$testoAzione = $testoAzione;
    }

    public function setTitoloPagina($titoloPagina) {
        self::$titoloPagina = $titoloPagina;
    }

    public function setConfermaTip($tip) {
        self::$confermaTip = $tip;
    }

    // Getters -----------------------------------------------------------------------------

    public function getTestata() {
        return self::$testata;
    }

    public function getPiede() {
        return self::$piede;
    }

    public function getAzione() {
        return self::$azione;
    }

    public function getTestoAzione() {
        return self::$testoAzione;
    }

    public function getTitoloPagina() {
        return self::$titoloPagina;
    }

    public function getConfermaTip() {
        return self::$confermaTip;
    }

    // Composizione del menu in testata pagine --------------------------------------------

    public function makeMenu($utility): string {

        $array = $utility->getConfig();

        $ambiente = $this->getIndexSession(self::AMBIENTE) !== NULL ? $this->getIndexSession(self::AMBIENTE) : $this->getEnvironment($array);

        // H o m e --------------------------------------

        $home = "";

//        <li class="dropdown">
//            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
//            <ul class="dropdown-menu">
//                <li><a href="#">Action</a></li>
//                <li><a href="#">Another action</a></li>
//                <li><a href="#">Something else here</a></li>
//                <li role="separator" class="divider"></li>
//                <li><a href="#">Separated link</a></li>
//                <li role="separator" class="divider"></li>
//                <li><a href="#">One more separated link</a></li>
//            </ul>
//        </li>


        if ($array["home"] == "Y") {
            $home .= "<li class='dropdown'>";
            $home .= "<a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>" . $array['home_menu_title'];
            $home .= "<span class='caret'></span></a>";
            $home .= "<ul class='dropdown-menu'>";

            if ($array["home_item_1"] == "Y")
                $home .= "<li><a href='../strumenti/cambiaContoStep1Facade.class.php?modo=start'>" . $array["home_item_1_name"] . "</a></li>";
            if ($array["home_item_2"] == "Y")
                $home .= "<li><a href='../strumenti/lavoriAutomaticiFacade.class.php?modo=start'>" . $array["home_item_2_name"] . "</a></li>";

            $home .= "</ul></li>";
        }
        $this->menu .= $home;

        // O p er a z i o n i ------------------------------------------------------------

        $operazioni = "";

        if ($array["operazioni"] == "Y") {
            $operazioni .= "<li class='dropdown'>";
            $operazioni .= "<a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>" . $array['operazioni_menu_title'];
            $operazioni .= "<span class='caret'></span></a>";
            $operazioni .= "<ul class='dropdown-menu'>";

            if ($array["operazioni_item_1"] == "Y")
                $operazioni .= "<li><a href='../primanota/ricercaRegistrazioneFacade.class.php?modo=start'>" . $array["operazioni_item_1_name"] . "</a></li>";
            
            $operazioni .= "</ul></li>";
        }
        $this->menu .= $operazioni;

        // A n a g r a f i c h e ------------------------------------------------------------

        $anagrafiche = "";

        if ($array["anagrafiche"] == "Y") {
            $anagrafiche .= "<li class='dropdown'>";
            $anagrafiche .= "<a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>" . $array['anagrafiche_menu_title'];
            $anagrafiche .= "<span class='caret'></span></a>";
            $anagrafiche .= "<ul class='dropdown-menu'>";

            if ($array["anagrafiche_item_3"] == "Y")
                $anagrafiche .= "<li><a href='../anagrafica/ricercaFornitoreFacade.class.php?modo=start'>" . $array["anagrafiche_item_3_name"] . "</a></li>";
            if ($array["anagrafiche_item_4"] == "Y")
                $anagrafiche .= "<li><a href='../anagrafica/ricercaClienteFacade.class.php?modo=start'>" . $array["anagrafiche_item_4_name"] . "</a></li>";
            if ($array["anagrafiche_item_5"] == "Y")
                $anagrafiche .= "<li><a href='../anagrafica/ricercaMercatoFacade.class.php?modo=start'>" . $array["anagrafiche_item_5_name"] . "</a></li>";

            $anagrafiche .= "</ul></li>";
        }
        $this->menu .= $anagrafiche;

        // C o n f i g u r a z i o n i ------------------------------------------------------------

        $configurazioni = "";

        if ($array["configurazioni"] == "Y") {
            $configurazioni .= "<li class='dropdown'>";
            $configurazioni .= "<a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>" . $array['configurazioni_menu_title'];
            $configurazioni .= "<span class='caret'></span></a>";
            $configurazioni .= "<ul class='dropdown-menu'>";

            if ($array["configurazioni_item_2"] == "Y")
                $configurazioni .= "<li><a href='../configurazioni/ricercaContoFacade.class.php?modo=start'>" . $array["configurazioni_item_2_name"] . "</a></li>";
            if ($array["configurazioni_item_4"] == "Y")
                $configurazioni .= "<li><a href='../configurazioni/ricercaCausaleFacade.class.php?modo=start'>" . $array["configurazioni_item_4_name"] . "</a></li>";
            if ($array["configurazioni_item_5"] == "Y")
                $configurazioni .= "<li><a href='../configurazioni/ricercaProgressivoFatturaFacade.class.php?modo=start'>" . $array["configurazioni_item_5_name"] . "</a></li>";

            $configurazioni .= "</ul></li>";
        }
        $this->menu .= $configurazioni;

        // S c a d e n z e ------------------------------------------------------------

        $scadenze = "";

        if ($array["scadenze"] == "Y") {
            $scadenze .= "<li class='dropdown'>";
            $scadenze .= "<a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>" . $array['scadenze_menu_title'];
            $scadenze .= "<span class='caret'></span></a>";
            $scadenze .= "<ul class='dropdown-menu'>";

            if ($array["scadenze_item_1"] == "Y")
                $scadenze .= "<li><a href='../scadenze/ricercaScadenzeFornitoreFacade.class.php?modo=start'>" . $array["scadenze_item_1_name"] . "</a></li>";
            if ($array["scadenze_item_2"] == "Y")
                $scadenze .= "<li><a href='../scadenze/ricercaScadenzeClienteFacade.class.php?modo=start'>" . $array["scadenze_item_2_name"] . "</a></li>";

            $scadenze .= "</ul></li>";
        }
        $this->menu .= $scadenze;

        // R i e p i o l o g h i ------------------------------------------------------------

        $riepiloghi = "";

        if ($array["riepiloghi"] == "Y") {
            $riepiloghi .= "<li class='dropdown'>";
            $riepiloghi .= "<a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>" . $array['riepiloghi_menu_title'];
            $riepiloghi .= "<span class='caret'></span></a>";
            $riepiloghi .= "<ul class='dropdown-menu'>";

            if ($array["riepiloghi_item_1"] == "Y")
                $riepiloghi .= "<li><a href='../riepiloghi/generaBilancioEsercizioFacade.class.php?modo=start'>" . $array["riepiloghi_item_1_name"] . "</a></li>";
            if ($array["riepiloghi_item_2"] == "Y")
                $riepiloghi .= "<li><a href='../riepiloghi/generaBilancioPeriodicoFacade.class.php?modo=start'>" . $array["riepiloghi_item_2_name"] . "</a></li>";

            $riepiloghi .= "<li role='separator' class='divider'></li>";

            if ($array["riepiloghi_item_3"] == "Y")
                $riepiloghi .= "<li><a href='../riepiloghi/riepilogoNegoziFacade.class.php?modo=start'>" . $array["riepiloghi_item_3_name"] . "</a></li>";
            if ($array["riepiloghi_item_4"] == "Y")
                $riepiloghi .= "<li><a href='../riepiloghi/andamentoNegoziFacade.class.php?modo=start'>" . $array["riepiloghi_item_4_name"] . "</a></li>";
            if ($array["riepiloghi_item_7"] == "Y")
                $riepiloghi .= "<li><a href='../riepiloghi/andamentoNegoziConfrontatoFacade.class.php?modo=start'>" . $array["riepiloghi_item_7_name"] . "</a></li>";
            if ($array["riepiloghi_item_8"] == "Y")
                $riepiloghi .= "<li><a href='../riepiloghi/andamentoMercatiFacade.class.php?modo=start'>" . $array["riepiloghi_item_8_name"] . "</a></li>";
            if ($array["riepiloghi_item_5"] == "Y")
                $riepiloghi .= "<li><a href='../saldi/ricercaSaldiFacade.class.php?modo=start'>" . $array["riepiloghi_item_5_name"] . "</a></li>";
            if ($array["riepiloghi_item_6"] == "Y")
                $riepiloghi .= "<li><a href='../saldi/creaSaldoFacade.class.php?modo=start'>" . $array["riepiloghi_item_6_name"] . "</a></li>";

            $riepiloghi .= "<li role='separator' class='divider'></li>";

            if ($array["riepiloghi_item_9"] == "Y")
                $riepiloghi .= "<li><a href='../riepiloghi/generaQuadroPresenzeAssistitiFacade.class.php?modo=start'>" . $array["riepiloghi_item_9_name"] . "</a></li>";            

            $riepiloghi .= "</ul></li>";
        }
        $this->menu .= $riepiloghi;

        // F a t t u r e ------------------------------------------------------------

        $fatture = "";

        if ($array["fatture"] == "Y") {
            $fatture .= "<li class='dropdown'>";
            $fatture .= "<a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>" . $array["fatture_menu_title"];
            $fatture .= "<span class='caret'></span></a>";
            $fatture .= "<ul class='dropdown-menu'>";

            if ($array["fatture_item_1"] == "Y")
                $fatture .= "<li><a href='../fatture/creaFatturaAziendaConsortileFacade.class.php?modo=start'>" . $array["fatture_item_1_name"] . "</a></li>";
            if ($array["fatture_item_2"] == "Y")
                $fatture .= "<li><a href='../fatture/creaFatturaEntePubblicoFacade.class.php?modo=start'>" . $array["fatture_item_2_name"] . "</a></li>";
            if ($array["fatture_item_3"] == "Y")
                $fatture .= "<li><a href='../fatture/creaFatturaClienteFacade.class.php?modo=start'>" . $array["fatture_item_3_name"] . "</a></li>";
            
            $fatture .= "<li><a href='../fatture/creaFatturaClienteXMLFacade.class.php?modo=start'>FatturaXML</a></li>";            
            $fatture .= "</ul></li>";
        }
        $this->menu .= $fatture;

        // S t r u m e n t i ------------------------------------------------------------

        $strumenti = "";
        
        if ($array["strumenti"] == "Y") {
            $strumenti .= "<li class='dropdown'>";
            $strumenti .= "<a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>" . $array["strumenti_menu_title"];
            $strumenti .= "<span class='caret'></span></a>";
            $strumenti .= "<ul class='dropdown-menu'>";
            
            if ($array["strumenti_item_1"] == "Y")
                $strumenti .= "<li><a href='../strumenti/cambiaContoStep1Facade.class.php?modo=start'>" . $array["strumenti_item_1_name"] . "</a></li>";

            $strumenti .= "<li role='separator' class='divider'></li>";
            
            if ($array["strumenti_item_2"] == "Y")
                $strumenti .= "<li><a href='../strumenti/importaExcelCorrispettiviNegozioStep1Facade.class.php?modo=start'>" . $array["strumenti_item_2_name"] . "</a></li>";
            
            if ($array["strumenti_item_3"] == "Y")
                $strumenti .= "<li><a href='../strumenti/importaExcelCorrispettiviMercatoStep1Facade.class.php?modo=start'>" . $array["strumenti_item_3_name"] . "</a></li>";
            
            if ($array["strumenti_item_4"] == "Y")
                $strumenti .= "<li><a href='../strumenti/importaExcelPresenzeAssistitiStep1Facade.class.php?modo=start'>" . $array["strumenti_item_4_name"] . "</a></li>";
            
            if ($array["strumenti_item_5"] == "Y")
                $strumenti .= "<li><a href='../strumenti/esecuzioneOnlineLavoriAutomaticiFacade.class.php?modo=start'>" . $array["strumenti_item_5_name"] . "</a></li>";

            $strumenti .= "</ul></li>";
        }
        $this->menu .= $strumenti;
        
        
        return $this->menu;
    }

    public function isAnnoBisestile($anno) {

        $annoBisestile = false;

        if (($anno % 4 == 0 && $anno % 100 != 0) || $anno % 400 == 0) {
            $annoBisestile = true;
        }
        return $annoBisestile;
    }

    public function sommaGiorniData($data, $carattereSeparatore, $giorniDaSommare) {

        list($giorno, $mese, $anno) = explode($carattereSeparatore, $data);
        return date("d-m-Y", mktime(0, 0, 0, $mese, $giorno + $giorniDaSommare, $anno));
    }

    public function sommaGiorniDataYMD($data, $carattereSeparatore, $giorniDaSommare) {

        list($anno, $mese, $giorno) = explode($carattereSeparatore, $data);
        return date("Y-m-d", mktime(0, 0, 0, $mese, $giorno + $giorniDaSommare, $anno));
    }

    function isEmpty($param) {
        if (($param == "") or ( $param == " ") or ( $param == null))
            return TRUE;
        else
            return FALSE;
    }

    function isNotEmpty($param) {
        if (($param != "") and ( $param != " ") and ( $param != null))
            return TRUE;
        else
            return FALSE;
    }

    public function caricaElencoFornitori($fornitore) {
        $elencoFornitori = "<option value=' '>&nbsp;</option>";
        foreach ($fornitore->getFornitori() as $unFornitore) {
            $elencoFornitori .= "<option value='" . $unFornitore[Fornitore::ID_FORNITORE] . "'>" . $unFornitore[Fornitore::DES_FORNITORE] . "</option>";
        }
        return $elencoFornitori;
    }

    public function caricaElencoClienti($cliente) {
        $elencoClienti = "<option value=' '>&nbsp;</option>";
        foreach ($cliente->getClienti() as $unCliente) {
            $elencoClienti .= "<option value='" . $unCliente[Cliente::ID_CLIENTE] . "'>" . $unCliente[Cliente::DES_CLIENTE] . "</option>";
        }
        return $elencoClienti;
    }

    /**
     * Questo metodo determina l'ambiente sulla bae degli utenti preenti loggati
     * @param array
     * @param _SESSION
     */
    public function getEnvironment($array) {

        $users = shell_exec("who | cut -d' ' -f1 | sort | uniq");
        $this->setIndexSession(self::USERS, $users);

        if (strpos($users, $array['usernameProdLogin']) === false) {
            $this->setIndexSession(self::AMBIENTE, "TEST");
        } else {
            $this->setIndexSession(self::AMBIENTE, "PROD");
        }
    }

// *******************************************
// *******************************************
// *******************************************

    /**
     * Questo metodo effettua un controllo sullo scadenziario dei fornitori.
     * Se ci sono scadenze superate restituisce un testo di notifica
     *
     * @param unknown $utility
     * @param unknown $db
     * @return string
     */
    public function controllaScadenzeFornitoriSuperate($utility, $db): string {

        $array = $utility->getConfig();
        $replace = array();
        $sqlTemplate = $this->root . $array['query'] . self::$queryControllaScadenzeFornitoreSuperate;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        $scadenze = "";

        foreach (pg_fetch_all($result) as $row) {
            $scadenze .= "&ndash; Pagamento scaduto il " . $row['dat_scadenza'] . " : " . $row['nota_scadenza'] . "<br/>";
        }
        return $scadenze;
    }

    /**
     * Questo metodo effettua un controllo sullo scadenziario dei clienti.
     * Se ci sono scadenze superate restituisce un testo di notifica
     *
     * @param unknown $utility
     * @param unknown $db
     * @return string
     */
    public function controllaScadenzeClientiSuperate($utility, $db): string {

        $array = $utility->getConfig();
        $replace = array();
        $sqlTemplate = $this->root . $array['query'] . self::$queryControllaScadenzeClienteSuperate;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        $scadenze = "";

        foreach (pg_fetch_all($result) as $row) {
            $scadenze .= "&ndash; Incasso scaduto il " . $row['dat_registrazione'] . " : " . $row['nota'] . "<br/>";
        }
        return $scadenze;
    }

    public function controllaRegistrazioniInErrore($utility, $db): string {

        $array = $utility->getConfig();
        $replace = array();
        $sqlTemplate = $this->root . $array['query'] . self::$queryControllaRegistrazioniInErrore;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        $scadenze = "";

        foreach (pg_fetch_all($result) as $row) {
            $scadenze .= "&ndash; Operazione errata del " . $row['dat_registrazione'] . " : " . $row['cod_negozio'] . " - " . $row['des_registrazione'] . "<br/>";
        }
        return $scadenze;
    }

    /**
     * Questo metodo setta come da eseguire le prima data utile di riporto saldo e tutte le successive
     * @param type $db
     * @param type $datRegistrazione
     */
    public function ricalcolaSaldi($db, $datRegistrazione) {
        $lavoroPianificato = LavoroPianificato::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        if ($array['lavoriPianificatiAttivati'] == "Si") {
            $lavoroPianificato->setDatRegistrazione(str_replace('/', '-', $datRegistrazione));
            $lavoroPianificato->settaDaEseguire($db);
        }
    }
    
    public function getParmFromRequest($parmName) {        
        if (null !== filter_input(INPUT_POST, $parmName)) {
            return filter_input(INPUT_POST, $parmName);            
        } else {
            if (null !== filter_input(INPUT_GET, $parmName)) {
                return filter_input(INPUT_GET, $parmName);            
            }            
        }
        return null;
    }
    
    public static function getInfoFromServer($infoName) {        
        if (null !== filter_input(INPUT_SERVER, $infoName)) {
            return filter_input(INPUT_SERVER, $infoName);            
        }
        return null;
    }
    
    public static function getIndexSession($indexName) {    
        return (isset($_SESSION[$indexName])) ? $_SESSION[$indexName] : null;
    }
    
    public static function setIndexSession($indexName, $indexValue) {
        $_SESSION[$indexName] = $indexValue;
    }
    
    public static function unsetIndexSessione($indexName) {
        unset($_SESSION[$indexName]);
    }
}

?>

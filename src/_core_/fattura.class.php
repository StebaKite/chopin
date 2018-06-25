<?php

require_once 'core.interface.php';
require_once 'coreBase.class.php';
require_once 'database.class.php';
require_once 'utility.class.php';

class Fattura extends CoreBase implements CoreInterface {

    private $root;

    /*
     *  Nomi dati della fattura
     */

    const DAT_FATTURA = "dat_fattura";
    const MES_RIFERIMENTO = "mes_riferimento";
    const DES_TITOLO = "des_titolo";
    const DES_CLIENTE = "des_cliente";
    const TIP_ADDEBITO = "tip_addebito";
    const COD_NEGOZIO = "cod_negozio";
    const NUM_FATTURA = "num_fattura";
    const DES_RAGSOC_BANCA = "des_ragsoc_banca";
    const COD_IBAN_BANCA = "cod_iban_banca";
    const DET_INSERITI = "det_inseriti";
    const IDX_DETTAGLI = "idx_dettagli";

    /*
     *  Dati fattura
     */

    private $datFattura;
    private $mesRiferimento;
    private $desTitolo;
    private $catCliente, $desCliente, $indCliente, $cittaCliente, $capCliente, $pivaCliente, $cfisCliente;
    private $tipAddebito;
    private $codNegozio;
    private $numFattura, $notaPiede;
    private $desRagsocBanca, $codIbanBanca;
    private $dettagli, $indexDettagli;
    private $anno, $nmese, $giorno;
    private $meserif, $mesenome;
    private $totDettagli, $totImponibile, $totIva;
    private $elencoAziendeConsortili;
    private $mese = array(
        '01' => 'Gennaio',
        '02' => 'Febbraio',
        '03' => 'Marzo',
        '04' => 'Aprile',
        '05' => 'Maggio',
        '06' => 'Giugno',
        '07' => 'Luglio',
        '08' => 'Agosto',
        '09' => 'Settembre',
        '10' => 'Ottobre',
        '11' => 'Novembre',
        '12' => 'Dicembre'
    );

    /*
     * Queries
     */

    const AGGIORNA_NUMERO_FATTURA = "/fatture/aggiornaNumeroFattura.sql";

    /*
     * Metodi
     *
     */

    function __construct() {
        $this->setRoot($_SERVER['DOCUMENT_ROOT']);
    }

    public function getInstance() {

        if (!isset($_SESSION[self::FATTURA]))
            $_SESSION[self::FATTURA] = serialize(new Fattura());
        return unserialize($_SESSION[self::FATTURA]);
    }

    public function prepara() {

        $this->setDatFattura(date("d/m/Y"));
        $this->setMesRiferimento(self::EMPTYSTRING);
        $this->setDesTitolo(self::EMPTYSTRING);
        $this->setDesCliente(self::EMPTYSTRING);
        $this->setTipAddebito(self::EMPTYSTRING);
        $this->setCodNegozio(self::EMPTYSTRING);
        $this->setNumFattura(self::EMPTYSTRING);
        $this->setDesRagsocBanca(self::EMPTYSTRING);
        $this->setCodIbanBanca(self::EMPTYSTRING);
        $this->setDettagli(self::EMPTYSTRING);
        $this->setIndexDettagli(self::EMPTYSTRING);
    }

    public function aggiornaNumeroFattura($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%cat_cliente%' => $this->getCatCliente(),
            '%neg_progr%' => $this->getCodNegozio(),
            '%num_fattura_ultimo%' => $this->getNumFattura()
        );

        $sqlTemplate = $this->getRoot() . $array['query'] . self::AGGIORNA_NUMERO_FATTURA;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        return $result;
    }

    // Getters & Setters

    public function getRoot() {
        return $this->root;
    }

    public function setRoot($root) {
        $this->root = $root;
    }

    public function getDatFattura() {
        return $this->datFattura;
    }

    public function setDatFattura($datFattura) {
        $this->datFattura = $datFattura;
    }

    public function getMesRiferimento() {
        return $this->mesRiferimento;
    }

    public function setMesRiferimento($mesRiferimento) {
        $this->mesRiferimento = $mesRiferimento;
    }

    public function getDesTitolo() {
        return $this->desTitolo;
    }

    public function setDesTitolo($desTitolo) {
        $this->desTitolo = $desTitolo;
    }

    public function getDesCliente() {
        return $this->desCliente;
    }

    public function setDesCliente($desCliente) {
        $this->desCliente = $desCliente;
    }

    public function getTipAddebito() {
        return $this->tipAddebito;
    }

    public function setTipAddebito($tipAddebito) {
        $this->tipAddebito = $tipAddebito;
    }

    public function getCodNegozio() {
        return $this->codNegozio;
    }

    public function setCodNegozio($codNegozio) {
        $this->codNegozio = $codNegozio;
    }

    public function getNumFattura() {
        return $this->numFattura;
    }

    public function setNumFattura($numFattura) {
        $this->numFattura = $numFattura;
    }

    public function getDesRagsocBanca() {
        return $this->desRagsocBanca;
    }

    public function setDesRagsocBanca($desRagsocBanca) {
        $this->desRagsocBanca = $desRagsocBanca;
    }

    public function getCodIbanBanca() {
        return $this->codIbanBanca;
    }

    public function setCodIbanBanca($codIbanBanca) {
        $this->codIbanBanca = $codIbanBanca;
    }

    public function getDettagli() {
        return $this->dettagli;
    }

    public function setDettagli($dettagli) {
        $this->dettagli = $dettagli;
    }

    public function getIndexDettagli() {
        return $this->indexDettagli;
    }

    public function setIndexDettagli($indexDettagli) {
        $this->indexDettagli = $indexDettagli;
    }

    public function getAnno() {
        return $this->anno;
    }

    public function setAnno($anno) {
        $this->anno = $anno;
    }

    public function getNmese() {
        return $this->nmese;
    }

    public function setNmese($nmese) {
        $this->nmese = $nmese;
    }

    public function getGiorno() {
        return $this->giorno;
    }

    public function setGiorno($giorno) {
        $this->giorno = $giorno;
    }

    public function getMeserif() {
        return $this->meserif;
    }

    public function setMeserif($meserif) {
        $this->meserif = $meserif;
    }

    public function getMesenome() {
        return $this->mesenome;
    }

    public function setMesenome($mesenome) {
        $this->mesenome = $mesenome;
    }

    public function getMese() {
        return $this->mese;
    }

    public function setMese($mese) {
        $this->mese = $mese;
    }

    public function getCatCliente() {
        return $this->catCliente;
    }

    public function setCatCliente($catCliente) {
        $this->catCliente = $catCliente;
    }

    public function getTotDettagli() {
        return $this->totDettagli;
    }

    public function setTotDettagli($totDettagli) {
        $this->totDettagli = $totDettagli;
    }

    public function getTotImponibile() {
        return $this->totImponibile;
    }

    public function setTotImponibile($totImponibile) {
        $this->totImponibile = $totImponibile;
    }

    public function getTotIva() {
        return $this->totIva;
    }

    public function setTotIva($totIva) {
        $this->totIva = $totIva;
    }

    public function getIndCliente() {
        return $this->indCliente;
    }

    public function setIndCliente($indCliente) {
        $this->indCliente = $indCliente;
    }

    public function getCittaCliente() {
        return $this->cittaCliente;
    }

    public function setCittaCliente($cittaCliente) {
        $this->cittaCliente = $cittaCliente;
    }

    public function getCapCliente() {
        return $this->capCliente;
    }

    public function setCapCliente($capCliente) {
        $this->capCliente = $capCliente;
    }

    public function getPivaCliente() {
        return $this->pivaCliente;
    }

    public function setPivaCliente($pivaCliente) {
        $this->pivaCliente = $pivaCliente;
    }

    public function getCfisCliente() {
        return $this->cfisCliente;
    }

    public function setCfisCliente($cfisCliente) {
        $this->cfisCliente = $cfisCliente;
    }

    public function getNotaPiede() {
        return $this->notaPiede;
    }

    public function setNotaPiede($notaPiede) {
        $this->notaPiede = $notaPiede;
    }

    public function getElencoAziendeConsortili() {
        return $this->elencoAziendeConsortili;
    }

    public function setElencoAziendeConsortili($elencoAziendeConsortili) {
        $this->elencoAziendeConsortili = $elencoAziendeConsortili;
    }

}

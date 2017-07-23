<?php

require_once 'core.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';

class Saldo implements CoreInterface {

	private $root;

	// Nomi colonne tabella Saldo

	const COD_NEGOZIO = "cod_negozio";
	const COD_CONTO = "cod_conto";
	const COD_SOTTOCONTO = "cod_sottoconto";
	const DAT_SALDO = "dat_saldo";
	const DES_SALDO = "des_saldo";
	const IMP_SALDO = "imp_saldo";
	const IND_DAREAVERE = "ind_dareavere";

	// dati saldo

	private $codNegozio;
	private $codConto;
	private $codSottoconto;
	private $datSaldo;
	private $desSaldo;
	private $impSaldo;
	private $indDareavere;

	private $saldi;
	private $qtaSaldi;

	// fitri di ricerca


	// Queries


	// Metodi

	function __construct() {
		$this->setRoot($_SERVER['DOCUMENT_ROOT']);
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::SALDO])) $_SESSION[self::SALDO] = serialize(new Saldo());
		return unserialize($_SESSION[self::SALDO]);
	}




	// Getters e Setters

    public function getRoot(){
        return $this->root;
    }

    public function setRoot($root){
        $this->root = $root;
    }

    public function getCodNegozio(){
        return $this->codNegozio;
    }

    public function setCodNegozio($codNegozio){
        $this->codNegozio = $codNegozio;
    }

    public function getCodConto(){
        return $this->codConto;
    }

    public function setCodConto($codConto){
        $this->codConto = $codConto;
    }

    public function getCodSottoconto(){
        return $this->codSottoconto;
    }

    public function setCodSottoconto($codSottoconto){
        $this->codSottoconto = $codSottoconto;
    }

    public function getDatSaldo(){
        return $this->datSaldo;
    }

    public function setDatSaldo($datSaldo){
        $this->datSaldo = $datSaldo;
    }

    public function getDesSaldo(){
        return $this->desSaldo;
    }

    public function setDesSaldo($desSaldo){
        $this->desSaldo = $desSaldo;
    }

    public function getImpSaldo(){
        return $this->impSaldo;
    }

    public function setImpSaldo($impSaldo){
        $this->impSaldo = $impSaldo;
    }

    public function getIndDareavere(){
        return $this->indDareavere;
    }

    public function setIndDareavere($indDareavere){
        $this->indDareavere = $indDareavere;
    }

    public function getSaldi(){
        return $this->saldi;
    }

    public function setSaldi($saldi){
        $this->saldi = $saldi;
    }

    public function getQtaSaldi(){
        return $this->qtaSaldi;
    }

    public function setQtaSaldi($qtaSaldi){
        $this->qtaSaldi = $qtaSaldi;
    }
}

?>
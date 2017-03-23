<?php

require_once 'core.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';

class DettaglioRegistrazione implements CoreInterface {

	private $root;

	// Nomi colonne tabella Registrazione

	const ID_DETTAGLIO_REGISTRAZIONE	= "id_dettaglio_registrazione";
	const ID_REGISTRAZIONE				= "id_registrazione";
	const IMP_REGISTRAZIONE				= "imp_registrazione";
	const IND_DAREAVERE					= "ind_dareavere";
	const COD_CONTO						= "cod_conto";
	const COD_SOTTOCONTO				= "cod_sottoconto";
	const DAT_INSERIMENTO				= "dat_inserimento";

	// dati registrazione

	private $idDettaglioRegistrazione;
	private $idRegistrazione;
	private $impRegistrazione;
	private $indDareavere;
	private $codConto;
	private $codSottoconto;
	private $datInserimento;

	// Queries


	// Metodi

	function __construct() {
		$this->setRoot($_SERVER['DOCUMENT_ROOT']);
	}

	public function getInstance() {

		if (!isset($_SESSION[self::DETTAGLIO_REGISTRAZIONE])) $_SESSION[self::DETTAGLIO_REGISTRAZIONE] = serialize(new DettaglioRegistrazione());
		return unserialize($_SESSION[self::DETTAGLIO_REGISTRAZIONE]);
	}









    public function getRoot(){
        return $this->root;
    }

    public function setRoot($root){
        $this->root = $root;
    }

    public function getIdDettaglioRegistrazione(){
        return $this->idDettaglioRegistrazione;
    }

    public function setIdDettaglioRegistrazione($idDettaglioRegistrazione){
        $this->idDettaglioRegistrazione = $idDettaglioRegistrazione;
    }

    public function getIdRegistrazione(){
        return $this->idRegistrazione;
    }

    public function setIdRegistrazione($idRegistrazione){
        $this->idRegistrazione = $idRegistrazione;
    }

    public function getImpRegistrazione(){
        return $this->impRegistrazione;
    }

    public function setImpRegistrazione($impRegistrazione){
        $this->impRegistrazione = $impRegistrazione;
    }

    public function getIndDareavere(){
        return $this->indDareavere;
    }

    public function setIndDareavere($indDareavere){
        $this->indDareavere = $indDareavere;
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

    public function getDatInserimento(){
        return $this->datInserimento;
    }

    public function setDatInserimento($datInserimento){
        $this->datInserimento = $datInserimento;
    }
}

?>
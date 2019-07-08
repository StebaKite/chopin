<?php

require_once 'core.interface.php';
require_once 'coreBase.class.php';
require_once 'database.class.php';
require_once 'utility.class.php';

class DettaglioRegistrazione extends CoreBase implements CoreInterface {

    private $root;

    // Nomi colonne tabella Registrazione

    const ID_DETTAGLIO_REGISTRAZIONE = "id_dettaglio_registrazione";
    const ID_REGISTRAZIONE = "id_registrazione";
    const IMP_REGISTRAZIONE = "imp_registrazione";
    const IND_DAREAVERE = "ind_dareavere";
    const COD_CONTO = "cod_conto";
    const COD_CONTO_COMPOSTO = "cod_conto_composto";
    const COD_SOTTOCONTO = "cod_sottoconto";
    const DAT_INSERIMENTO = "dat_inserimento";
    const IND_CONTO_PRINCIPALE = "ind_conto_principale";

    // dati registrazione

    private $idDettaglioRegistrazione;
    private $idRegistrazione;
    private $impRegistrazione;
    private $indDareavere;
    private $codConto;
    private $codSottoconto;
    private $codContoComposto;
    private $datInserimento;
    private $dettagliRegistrazione;
    private $qtaDettagliRegistrazione;
    private $totDare;
    private $totAvere;
    private $aliquota;
    private $impIva;
    private $imponibile;
    private $indContoPrincipale;
    
    // dati per controlli in pagina

    private $campoMsgControlloPagina;
    private $idTablePagina;
    private $msgControlloPagina;
    private $nomeCampo;
    private $labelNomeCampo;

    // Queries

    const CREA_DETTAGLIO_REGISTRAZIONE = "/primanota/creaDettaglioRegistrazione.sql";
    const CERCA_DETTAGLI_REGISTRAZIONE = "/primanota/leggiDettagliRegistrazione.sql";
    const AGGIORNA_DETTAGLIO_REGISTRAZIONE = "/primanota/aggiornaDettaglioRegistrazione.sql";
    const CANCELLA_DETTAGLIO_REGISTRAZIONE = "/primanota/deleteDettaglioRegistrazione.sql";
    const AGGIORNA_CONTO_DETTAGLIO_REGISTRAZIONE = "/strumenti/aggiornaContoDettaglioRegistrazione.sql";

    // Metodi

    function __construct() {
        $this->setRoot($_SERVER['DOCUMENT_ROOT']);
    }

    public static function getInstance() {

        if (!isset($_SESSION[self::DETTAGLIO_REGISTRAZIONE]))
            $_SESSION[self::DETTAGLIO_REGISTRAZIONE] = serialize(new DettaglioRegistrazione());
        return unserialize($_SESSION[self::DETTAGLIO_REGISTRAZIONE]);
    }

    public function prepara() {
        $this->setDettagliRegistrazione(null);
        $this->setQtaDettagliRegistrazione(0);
        $_SESSION[self::DETTAGLIO_REGISTRAZIONE] = serialize($this);
    }

    public function leggiDettagliRegistrazione($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $replace = array(
            '%id_registrazione%' => trim($this->getIdRegistrazione())
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::CERCA_DETTAGLI_REGISTRAZIONE;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            $this->setDettagliRegistrazione(pg_fetch_all($result));
            $this->setQtaDettagliRegistrazione(pg_num_rows($result));
        } else {
            $this->setDettagliRegistrazione(null);
            $this->setQtaDettagliRegistrazione(0);
        }
        $_SESSION[self::DETTAGLIO_REGISTRAZIONE] = serialize($this);
        return $result;
    }

    public function getImportoContoPrincipale() {
        
        $importoContoPrincipale = 0;
        foreach ($this->getDettagliRegistrazione() as $unDettaglio) {
            if ($unDettaglio[DettaglioRegistrazione::IND_CONTO_PRINCIPALE] == "Y") {
                $importoContoPrincipale = $unDettaglio[DettaglioRegistrazione::IMP_REGISTRAZIONE];
                break;
            }
        }
        return $importoContoPrincipale;
    }
    
    
    public function aggiornaDettaglio() {

        // aggiorno array dei dettagli        
    
        $dettagliDiff = array();
        foreach ($this->getDettagliRegistrazione() as $unDettaglio) {

            $contoComposto = explode(" - ", $unDettaglio[DettaglioRegistrazione::COD_CONTO]);
            $codConto = explode(".", $contoComposto[0]);

            if ((trim($codConto[0]) != trim($this->getCodConto())) or ( trim($codConto[1]) != trim($this->getCodSottoconto())))
                array_push($dettagliDiff, $unDettaglio);
            else {
                $item = array(
                    DettaglioRegistrazione::ID_DETTAGLIO_REGISTRAZIONE => $unDettaglio[DettaglioRegistrazione::ID_DETTAGLIO_REGISTRAZIONE],
                    DettaglioRegistrazione::ID_REGISTRAZIONE => $unDettaglio[DettaglioRegistrazione::ID_REGISTRAZIONE],
                    DettaglioRegistrazione::IMP_REGISTRAZIONE => $this->getImpRegistrazione(),
                    DettaglioRegistrazione::IND_DAREAVERE => $this->getIndDareavere(),
                    DettaglioRegistrazione::COD_CONTO => $unDettaglio[DettaglioRegistrazione::COD_CONTO],
                    DettaglioRegistrazione::COD_CONTO_COMPOSTO => $unDettaglio[DettaglioRegistrazione::COD_CONTO_COMPOSTO],
                    DettaglioRegistrazione::COD_SOTTOCONTO => $unDettaglio[DettaglioRegistrazione::COD_SOTTOCONTO],
                    DettaglioRegistrazione::DAT_INSERIMENTO => $unDettaglio[DettaglioRegistrazione::DAT_INSERIMENTO],
                    DettaglioRegistrazione::IND_CONTO_PRINCIPALE => $unDettaglio[DettaglioRegistrazione::IND_CONTO_PRINCIPALE]
                );
                array_push($dettagliDiff, $item);
            }
        }
        $this->setDettagliRegistrazione($dettagliDiff);
        $_SESSION[self::DETTAGLIO_REGISTRAZIONE] = serialize($this);
    }
        
    public function aggiungi() {

        $aggiungiDettaglio = TRUE;
        foreach ($this->getDettagliRegistrazione() as $unDettaglio) {

            $contoComposto = explode(" - ", $unDettaglio[DettaglioRegistrazione::COD_CONTO_COMPOSTO]);
            $codConto = explode(".", $contoComposto[0]);
        
            if ((trim($codConto[0]) == trim($this->getCodConto())) and ( trim($codConto[1]) == trim($this->getCodSottoconto()))) {
                $aggiungiDettaglio = FALSE;
            }
        }

        if ($aggiungiDettaglio) {
            $item = array(
                DettaglioRegistrazione::ID_DETTAGLIO_REGISTRAZIONE => trim($this->getIdDettaglioRegistrazione()),
                DettaglioRegistrazione::ID_REGISTRAZIONE => $this->getIdRegistrazione(),
                DettaglioRegistrazione::IMP_REGISTRAZIONE => trim($this->getImpRegistrazione()),
                DettaglioRegistrazione::IND_DAREAVERE => trim($this->getIndDareavere()),
                DettaglioRegistrazione::COD_CONTO => trim($this->getCodContoComposto()),
                DettaglioRegistrazione::COD_CONTO_COMPOSTO => trim($this->getCodContoComposto()),
                DettaglioRegistrazione::COD_SOTTOCONTO => trim($this->getCodSottoconto()),
                DettaglioRegistrazione::DAT_INSERIMENTO => date("Y/m/d"),
                DettaglioRegistrazione::IND_CONTO_PRINCIPALE => trim($this->getIndContoPrincipale())                
            );

            if ($this->getQtaDettagliRegistrazione() == 0) {
                $resultset = array();
                array_push($resultset, $item);
                $this->setDettagliRegistrazione($resultset);
            } else {
                array_push($this->dettagliRegistrazione, $item);
                sort($this->dettagliRegistrazione);
            }
            $this->setQtaDettagliRegistrazione($this->getQtaDettagliRegistrazione() + 1);
            $_SESSION[self::DETTAGLIO_REGISTRAZIONE] = serialize($this);            
        }
    }

    public function aggiungiDettagliCorrispettivoMercato($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        
        $this->setIdDettaglioRegistrazione(0);
        $this->setIdRegistrazione(0);

        /**
         * Dettaglio sul conto selezionato
         */
        $sottoconto = Sottoconto::getInstance();
        
        $_cc = explode(".", $this->getCodConto());
        $sottoconto->setCodConto($_cc[0]);
        $sottoconto->setCodSottoconto($_cc[1]);
        $sottoconto->leggi($db);
        $sottoconto->searchSottoconto($_cc[1]);

        $this->setCodContoComposto($sottoconto->getCodConto() . "." . $sottoconto->getCodSottoconto() . " - " . $sottoconto->getDesSottoconto());
        $this->setIndDareAvere("D");
        $this->aggiungi();

        /**
         * Dettaglio conto erario
         */
        $_cc = explode(".", $array['contoErarioMercati']);
        $sottoconto->setCodConto($_cc[0]);
        $sottoconto->setCodSottoconto($_cc[1]);
        $sottoconto->leggi($db);
        $sottoconto->searchSottoconto($_cc[1]);
        
        $this->setCodContoComposto($sottoconto->getCodConto() . "." . $sottoconto->getCodSottoconto() . " - " . $sottoconto->getDesSottoconto());
        $this->setCodConto($_cc[0]);
        $this->setCodSottoconto($_cc[1]);
        $this->setImpRegistrazione($this->getImpIva());
        $this->setIndDareAvere("A");
        $this->aggiungi();

        /**
         * Dettaglio Cassa/Banca
         */
        $_cc = explode(".", $array['contoCorrispettivoMercati']);
        $sottoconto->setCodConto($_cc[0]);
        $sottoconto->setCodSottoconto($_cc[1]);
        $sottoconto->leggi($db);
        $sottoconto->searchSottoconto($_cc[1]);
        
        $this->setCodContoComposto($sottoconto->getCodConto() . "." . $sottoconto->getCodSottoconto() . " - " . $sottoconto->getDesSottoconto());
        $this->setCodConto($_cc[0]);
        $this->setCodSottoconto($_cc[1]);
        $this->setImpRegistrazione($this->getImponibile());
        $this->setIndDareAvere("A");
        $this->aggiungi();

        $_SESSION[self::DETTAGLIO_REGISTRAZIONE] = serialize($this);
    }
    
    public function aggiungiDettagliCorrispettivoNegozio($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $this->setIdDettaglioRegistrazione(0);
        $this->setIdRegistrazione(0);

        /**
         * Dettaglio sul conto selezionato
         */
        $sottoconto = Sottoconto::getInstance();
        
        $_cc = explode(".", $this->getCodConto());
        $sottoconto->setCodConto($_cc[0]);
        $sottoconto->setCodSottoconto($_cc[1]);
        $sottoconto->leggi($db);
        $sottoconto->searchSottoconto($_cc[1]);

        $this->setCodContoComposto($sottoconto->getCodConto() . "." . $sottoconto->getCodSottoconto() . " - " . $sottoconto->getDesSottoconto());
        $this->setCodConto($_cc[0]);
        $this->setCodSottoconto($_cc[1]);
        $this->setIndDareAvere("D");
        $this->aggiungi();

        /**
         * Dettaglio conto erario
         */
        $_cc = explode(".", $array['contoErarioNegozi']);
        $sottoconto->setCodConto($_cc[0]);
        $sottoconto->setCodSottoconto($_cc[1]);
        $sottoconto->leggi($db);
        $sottoconto->searchSottoconto($_cc[1]);
        
        $this->setCodContoComposto($sottoconto->getCodConto() . "." . $sottoconto->getCodSottoconto() . " - " . $sottoconto->getDesSottoconto());
        $this->setCodConto($_cc[0]);
        $this->setCodSottoconto($_cc[1]);
        $this->setImpRegistrazione($dettaglioRegistrazione->getImpIva());
        $this->setIndDareAvere("A");
        $this->aggiungi();

        /**
         * Dettaglio Cassa/Banca
         */
        $_cc = explode(".", $array['contoCorrispettivoNegozi']);
        $sottoconto->setCodConto($_cc[0]);
        $sottoconto->setCodSottoconto($_cc[1]);
        $sottoconto->leggi($db);
        $sottoconto->searchSottoconto($_cc[1]);
        
        $this->setCodContoComposto($sottoconto->getCodConto() . "." . $sottoconto->getCodSottoconto() . " - " . $sottoconto->getDesSottoconto());
        $this->setCodConto($_cc[0]);
        $this->setCodSottoconto($_cc[1]);
        $this->setImpRegistrazione($dettaglioRegistrazione->getImponibile());
        $this->setIndDareAvere("A");
        $this->aggiungi();

        $_SESSION[self::DETTAGLIO_REGISTRAZIONE] = serialize($this);
    }    
    
    public function cancella($db) {
        $dettagliDiff = array();
        foreach ($this->getDettagliRegistrazione() as $unDettaglio) {
            $conto = explode(" - ", trim($unDettaglio[self::COD_CONTO]));
            
            if (trim($conto[0]) != trim($this->getCodConto())) {
                array_push($dettagliDiff, $unDettaglio);
            } else {
                $utility = Utility::getInstance();
                $array = $utility->getConfig();
                $replace = array(
                    '%id_dettaglio_registrazione%' => trim($unDettaglio[self::ID_DETTAGLIO_REGISTRAZIONE])
                );

                $sqlTemplate = $this->root . $array['query'] . self::CANCELLA_DETTAGLIO_REGISTRAZIONE;
                $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
                $result = $db->execSql($sql);

                if ($result)
                    $this->setQtaDettagliRegistrazione($this->getQtaDettagliRegistrazione() - 1);
            }
        }
        $this->setDettagliRegistrazione($dettagliDiff);
        $_SESSION[self::DETTAGLIO_REGISTRAZIONE] = serialize($this);
    }

    public function inserisci($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%id_registrazione%' => trim($this->getIdRegistrazione()),
            '%imp_registrazione%' => trim($this->getImpRegistrazione()),
            '%ind_dareavere%' => trim($this->getIndDareavere()),
            '%cod_conto%' => trim($this->getCodConto()),
            '%cod_sottoconto%' => trim($this->getCodSottoconto())
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::CREA_DETTAGLIO_REGISTRAZIONE;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);
        return $result;
    }

    public function verificaQuadratura() {
        if ($this->getQtaDettagliRegistrazione() > 0) {
            $totale = 0;
            $totDare = 0;
            $totAvere = 0;
            foreach ($this->getDettagliRegistrazione() as $unDettaglio) {
                $importo = trim($unDettaglio[self::IMP_REGISTRAZIONE]);
                $indDareAvere = trim($unDettaglio[self::IND_DAREAVERE]);
                if ($indDareAvere == "D")
                    $totDare += $importo;
                if ($indDareAvere == "A")
                    $totAvere += $importo;
            }

            if (($totDare == 0) and ( $totAvere == 0))
                return false;
            else {
                $totale = round($totDare, 2) - round($totAvere, 2);
                $tot = round($totale,2);
                if ($tot == 0)
                    return true;
                else
                    return false;
            }
        } else
            return false;
    }

    public function aggiorna($db) {
        /**
         * Aggiorno l'importo sulla tabella DB
         */
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%ind_dareavere%' => $this->getIndDareavere(),
            '%imp_registrazione%' => $this->getImpRegistrazione(),
            '%id_dettaglio_registrazione%' => trim($this->getIdDettaglioRegistrazione()),
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::AGGIORNA_DETTAGLIO_REGISTRAZIONE;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);
        
        return $result;
    }
    
    public function aggiornaConto($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        
        $replace = array(
            '%id_dettaglio_registrazione%' => trim($this->getIdRegistrazione()),
            '%cod_conto%' => trim($this->getCodConto()),
            '%cod_sottoconto%' => trim($this->getCodSottoconto())
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::AGGIORNA_CONTO_DETTAGLIO_REGISTRAZIONE;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);

        $result = $db->execSql($sql);
        return $result;
    }
    

    public function getRoot() {
        return $this->root;
    }

    public function setRoot($root) {
        $this->root = $root;
    }

    public function getIdDettaglioRegistrazione() {
        return $this->idDettaglioRegistrazione;
    }

    public function setIdDettaglioRegistrazione($idDettaglioRegistrazione) {
        $this->idDettaglioRegistrazione = $idDettaglioRegistrazione;
    }

    public function getIdRegistrazione() {
        return $this->idRegistrazione;
    }

    public function setIdRegistrazione($idRegistrazione) {
        $this->idRegistrazione = $idRegistrazione;
    }

    public function getImpRegistrazione() {
        return $this->impRegistrazione;
    }

    public function setImpRegistrazione($impRegistrazione) {
        $this->impRegistrazione = $impRegistrazione;
    }

    public function getIndDareavere() {
        return $this->indDareavere;
    }

    public function setIndDareavere($indDareavere) {
        $this->indDareavere = $indDareavere;
    }

    public function getCodConto() {
        return $this->codConto;
    }

    public function setCodConto($codConto) {
        $this->codConto = $codConto;
    }

    public function getCodSottoconto() {
        return $this->codSottoconto;
    }

    public function setCodSottoconto($codSottoconto) {
        $this->codSottoconto = $codSottoconto;
    }

    public function getDatInserimento() {
        return $this->datInserimento;
    }

    public function setDatInserimento($datInserimento) {
        $this->datInserimento = $datInserimento;
    }

    public function getDettagliRegistrazione() {
        return $this->dettagliRegistrazione;
    }

    public function setDettagliRegistrazione($dettagliRegistrazione) {
        $this->dettagliRegistrazione = $dettagliRegistrazione;
    }

    public function getQtaDettagliRegistrazione() {
        return $this->qtaDettagliRegistrazione;
    }

    public function setQtaDettagliRegistrazione($qtaDettagliRegistrazione) {
        $this->qtaDettagliRegistrazione = $qtaDettagliRegistrazione;
    }

    public function getTotDare() {
        return $this->totDare;
    }

    public function setTotDare($totDare) {
        $this->totDare = $totDare;
    }

    public function getTotAvere() {
        return $this->totAvere;
    }

    public function setTotAvere($totAvere) {
        $this->totAvere = $totAvere;
    }

    public function getAliquota() {
        return $this->aliquota;
    }

    public function setAliquota($aliquota) {
        $this->aliquota = $aliquota;
    }

    public function getImpIva() {
        return $this->impIva;
    }

    public function setImpIva($impIva) {
        $this->impIva = $impIva;
    }

    public function getImponibile() {
        return $this->imponibile;
    }

    public function setImponibile($imponibile) {
        $this->imponibile = $imponibile;
    }

    public function getIdTablePagina() {
        return $this->idTablePagina;
    }

    public function setIdTablePagina($idTablePagina) {
        $this->idTablePagina = $idTablePagina;
    }

    public function getMsgControlloPagina() {
        return $this->msgControlloPagina;
    }

    public function setMsgControlloPagina($msgControlloPagina) {
        $this->msgControlloPagina = $msgControlloPagina;
    }

    public function getNomeCampo() {
        return $this->nomeCampo;
    }

    public function setNomeCampo($nomeCampo) {
        $this->nomeCampo = $nomeCampo;
    }

    public function getLabelNomeCampo() {
        return $this->labelNomeCampo;
    }

    public function setLabelNomeCampo($labelNomeCampo) {
        $this->labelNomeCampo = $labelNomeCampo;
    }

    public function getCampoMsgControlloPagina() {
        return $this->campoMsgControlloPagina;
    }

    public function setCampoMsgControlloPagina($campoMsgControlloPagina) {
        $this->campoMsgControlloPagina = $campoMsgControlloPagina;
    }

    public function getIndContoPrincipale() {
        return $this->indContoPrincipale;
    }

    public function setIndContoPrincipale($indContoPrincipale) {
        $this->indContoPrincipale = $indContoPrincipale;
    }
    
    function getCodContoComposto() {
        return $this->codContoComposto;
    }

    function setCodContoComposto($codContoComposto) {
        $this->codContoComposto = $codContoComposto;
    }

}
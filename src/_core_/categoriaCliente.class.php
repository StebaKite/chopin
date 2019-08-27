<?php

require_once 'core.interface.php';
require_once 'coreBase.class.php';
require_once 'database.class.php';
require_once 'utility.class.php';

class CategoriaCliente extends CoreBase implements CoreInterface {

    public $root;

    // Nomi colonne tabella Categorie_Cliente

    const CAT_CLIENTE = "cat_cliente";
    const DES_CATEGORIA = "des_categoria";

    // dati Cliente

    private $cat_cliente;
    private $des_categoria;
    // Altri dati funzionali

    private $elencoCategorieCliente;

    // Queries

    const LEGGI_CATEGORIE_CLIENTE = "/anagrafica/leggiCategorieCliente.sql";

    // Metodi

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::CATEGORIA_CLIENTE])) {
            $_SESSION[self::CATEGORIA_CLIENTE] = serialize(new CategoriaCliente());
        }
        return unserialize($_SESSION[self::CATEGORIA_CLIENTE]);
    }

    /**
     * Questo metodo legge tutte le categorie disponibili
     */
    public function load() {
        $cliente = Cliente::getInstance();
        $db = Database::getInstance();
        $utility = Utility::getInstance();

        $array = $utility->getConfig();

        $sqlTemplate = $this->getRoot() . $array['query'] . self::LEGGI_CATEGORIE_CLIENTE;
        $sql = $utility->getQueryTemplate($sqlTemplate);
        $result = $db->getData($sql);

        $elecat = "";

        foreach (pg_fetch_all($result) as $row) {

            if (trim($row['cat_cliente']) == trim($cliente->getCatCliente())) {
                $elecat .= "<option value='" . trim($row[self::CAT_CLIENTE]) . "' selected >" . trim($row[self::DES_CATEGORIA]) . "</option>";
            } else {
                $elecat .= "<option value='" . trim($row[self::CAT_CLIENTE]) . "'>" . trim($row[self::DES_CATEGORIA]) . "</option>";
            }
        }
        $this->setElencoCategorieCliente($elecat);
    }

    public function getRoot() {
        return $this->root;
    }

    public function setRoot($root) {
        $this->root = $root;
    }

    public function getCatCliente() {
        return $this->cat_cliente;
    }

    public function setCatCliente($cat_cliente) {
        $this->cat_cliente = $cat_cliente;
    }

    public function getDesCategoria() {
        return $this->des_categoria;
    }

    public function setDesCategoria($des_categoria) {
        $this->des_categoria = $des_categoria;
    }

    public function getElencoCategorieCliente() {
        return $this->elencoCategorieCliente;
    }

    public function setElencoCategorieCliente($elencoCategorieCliente) {
        $this->elencoCategorieCliente = $elencoCategorieCliente;
    }

}
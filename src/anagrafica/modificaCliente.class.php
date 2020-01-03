<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.business.interface.php';
require_once 'ricercaCliente.class.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'cliente.class.php';
require_once 'categoriaCliente.class.php';

class ModificaCliente extends AnagraficaAbstract implements AnagraficaBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::MODIFICA_CLIENTE) === NULL) {
            parent::setIndexSession(self::MODIFICA_CLIENTE, serialize(new ModificaCliente()));
        }
        return unserialize(parent::getIndexSession(self::MODIFICA_CLIENTE));
    }

    public function start() {
        $cliente = Cliente::getInstance();
        $db = Database::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $cliente->leggi($db);
        parent::setIndexSession(self::CLIENTE, serialize($cliente));

        $categoriaCliente = CategoriaCliente::getInstance();
        $categoriaCliente->setCatCliente(trim($cliente->getCatCliente()));
        $categoriaCliente->load();
        parent::setIndexSession(self::CATEGORIA_CLIENTE_OBJ, serialize($categoriaCliente));

        $risultato_xml = $this->root . $array['template'] . self::XML_CLIENTE;

        $replace = array(
            '%categoria%' => trim($cliente->getCatCliente()),
            '%codice%' => trim($cliente->getCodCliente()),
            '%descrizione%' => trim($cliente->getDesCliente()),
            '%indirizzo%' => trim($cliente->getDesIndirizzoCliente()),
            '%citta%' => trim($cliente->getDesCittaCliente()),
            '%cap%' => trim($cliente->getCapCliente()),
            '%tipoAddebito%' => trim($cliente->getTipAddebito()),
            '%partitaIva%' => trim($cliente->getCodPiva()),
            '%codiceFiscale%' => trim($cliente->getCodFisc()),
            '%categorieCliente%' => trim($categoriaCliente->getElencoCategorieCliente())
        );
        $template = $utility->tailFile($utility->getTemplate($risultato_xml), $replace);
        echo $utility->tailTemplate($template);
    }

    public function go() {
        $cliente = Cliente::getInstance();
        $db = Database::getInstance();

        $db->beginTransaction();

        if ($cliente->update($db)) {
            $db->commitTransaction();
        } else {
            $db->rollbackTransaction();
        }

        parent::setIndexSession("Obj_anagraficacontroller", serialize(new AnagraficaController(RicercaCliente::getInstance())));
        $controller = unserialize(parent::getIndexSession("Obj_anagraficacontroller"));
        $controller->start();
    }

}
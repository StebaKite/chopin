<?php

require_once 'fattura.abstract.class.php';
require_once 'fatture.business.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'fattura.class.php';
require_once 'cliente.class.php';
require_once 'fornitore.class.php';

class CreaFatturaAziendaConsortileTemplate extends FatturaAbstract implements FattureBusinessInterface {

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::CREA_FATTURA_AZIENDA_CONSORTILE_TEMPLATE]))
            $_SESSION[self::CREA_FATTURA_AZIENDA_CONSORTILE_TEMPLATE] = serialize(new CreaFatturaAziendaConsortileTemplate());
        return unserialize($_SESSION[self::CREA_FATTURA_AZIENDA_CONSORTILE_TEMPLATE]);
    }

    public function inizializzaPagina() {

    }

    public function controlliLogici() {

    }

    public function displayPagina() {

        $db = Database::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $fattura = Fattura::getInstance();
        $cliente = Cliente::getInstance();

        $cliente->load($db);

        $form = $this->root . $array['template'] . self::PAGINA_CREA_FATTURA_AZIENDA_CONSORTILE;

        $replace = array(
            '%titoloPagina%' => parent::getIndexSession(self::TITOLO_PAGINA),
            '%azione%' => parent::getIndexSession(self::AZIONE),
            '%confermaTip%' => parent::getIndexSession(self::TIP_CONFERMA),
            '%titolo%' => parent::getIndexSession($fattura->getDesTitolo()),
            '%numfat%' => $fattura->getNumFattura(),
            '%datafat%' => $fattura->getDatFattura(),
            '%empty_selected%' => (parent::isEmpty($fattura->getMesRiferimento())) ? "selected" : "",
            '%gen_selected%' => ($fattura->getMesRiferimento() == "Gennaio") ? "selected" : "",
            '%feb_selected%' => ($fattura->getMesRiferimento() == "Febbraio") ? "selected" : "",
            '%mar_selected%' => ($fattura->getMesRiferimento() == "Marzo") ? "selected" : "",
            '%apr_selected%' => ($fattura->getMesRiferimento() == "Aprile") ? "selected" : "",
            '%mag_selected%' => ($fattura->getMesRiferimento() == "Maggio") ? "selected" : "",
            '%giu_selected%' => ($fattura->getMesRiferimento() == "Giugno") ? "selected" : "",
            '%lug_selected%' => ($fattura->getMesRiferimento() == "Luglio") ? "selected" : "",
            '%ago_selected%' => ($fattura->getMesRiferimento() == "Agosto") ? "selected" : "",
            '%set_selected%' => ($fattura->getMesRiferimento() == "Settembre") ? "selected" : "",
            '%ott_selected%' => ($fattura->getMesRiferimento() == "Ottobre") ? "selected" : "",
            '%nov_selected%' => ($fattura->getMesRiferimento() == "Novembre") ? "selected" : "",
            '%dic_selected%' => ($fattura->getMesRiferimento() == "Dicembre") ? "selected" : "",
            '%tipoadd%' => $fattura->getTipAddebito(),
            '%ragsocbanca%' => str_replace("'", "&apos;", $fattura->getDesRagsocBanca()),
            '%ibanbanca%' => $fattura->getCodIbanBanca(),
            '%descli%' => $fattura->getDesCliente(),
            '%villa-checked%' => ($fattura->getCodNegozio() == self::VILLA) ? self::CHECK_THIS_ITEM : "",
            '%brembate-checked%' => ($fattura->getCodNegozio() == self::BREMBATE) ? self::CHECK_THIS_ITEM : "",
            '%trezzo-checked%' => ($fattura->getCodNegozio() == self::TREZZO) ? self::CHECK_THIS_ITEM : "",
            '%elenco_clienti%' => $this->caricaElencoClienti($cliente)
        );

        $template = $utility->tailFile($utility->getTemplate($form), $replace);
        echo $utility->tailTemplate($template);
    }

    public function go() {

    }

    public function start() {

    }

}
?>




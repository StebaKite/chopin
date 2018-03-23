<?php

require_once 'nexus6.abstract.class.php';
require_once 'scadenze.presentation.interface.php';

abstract class ScadenzeAbstract extends Nexus6Abstract implements ScadenzePresentationInterface {

//	const VISUALIZZA_REGISTRAZIONE_HREF = "<a onclick='visualizzaRegistrazione(";

    private static $_instance = null;
    public static $messaggio;
    // Query ---------------------------------------------------------------

    public static $queryUpdateStatoScadenzaCliente = "/scadenze/updateStatoScadenzaCliente.sql";

    // Getters e Setters ---------------------------------------------------

    public function setMessaggio($messaggio) {
        self::$messaggio = $messaggio;
    }

    // ------------------------------------------------

    public function getMessaggio() {
        return self::$messaggio;
    }

    // Metodi comuni di utilita della prima note ---------------------------

    public function leggiScadenze($db, $utility, $datascad_da, $datascad_a) {

        $array = $utility->getConfig();
        $replace = array(
            '%dat_scadenza_da%' => $datascad_da,
            '%dat_scadenza_a%' => $datascad_a
        );

        $sqlTemplate = self::$root . $array['query'] . self::$queryRicercaScadenze;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);
        return $result;
    }

    public function cambiaStatoScadenzaCliente($db, $utility, $idscadenza, $statoScadenza) {

        $array = $utility->getConfig();
        $replace = array(
            '%id_scadenza%' => trim($idscadenza),
            '%sta_scadenza%' => trim($statoScadenza)
        );
        $sqlTemplate = self::$root . $array['query'] . self::$queryUpdateStatoScadenzaCliente;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);
    }

    public function makeTabellaReadOnlyRegistrazioneOriginale($registrazione) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $bottoneVisualizzaRegistrazione = self::VISUALIZZA_REGISTRAZIONE_HREF . $registrazione->getIdRegistrazione() . self::VISUALIZZA_ICON;

        $thead = "<thead>" .
                "	<tr>" .
                "		<th>%ml.datReg%</th>" .
                "		<th>%ml.descreg%</th>" .
                "		<th>%ml.negozio%</th>" .
                "		<th></th>" .
                "	</tr>" .
                "</thead>";

        $tbody = "<tbody>" .
                "	<tr>" .
                "		<td>" . date("d/m/Y", strtotime($registrazione->getDatRegistrazione())) . "</td>" .
                "		<td>" . trim($registrazione->getDesRegistrazione()) . "</td>" .
                "		<td>" . trim($registrazione->getCodNegozio()) . "</td>" .
                "		<td>" . $bottoneVisualizzaRegistrazione . "</td>" .
                "	</tr>" .
                "</tbody>";

        return $thead . $tbody;
    }

    public function makeTabellaReadOnlyPagamento($registrazione) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $bottoneVisualizzaRegistrazione = self::VISUALIZZA_PAGAMENTO_HREF . $registrazione->getIdRegistrazione() . self::VISUALIZZA_ICON;

        $thead = "<thead>" .
                "	<tr>" .
                "		<th>%ml.datReg%</th>" .
                "		<th>%ml.descreg%</th>" .
                "		<th>%ml.negozio%</th>" .
                "		<th></th>" .
                "	</tr>" .
                "</thead>";

        $tbody = "<tbody>" .
                "	<tr>" .
                "		<td>" . date("d/m/Y", strtotime($registrazione->getDatRegistrazione())) . "</td>" .
                "		<td>" . trim($registrazione->getDesRegistrazione()) . "</td>" .
                "		<td>" . trim($registrazione->getCodNegozio()) . "</td>" .
                "		<td>" . $bottoneVisualizzaRegistrazione . "</td>" .
                "	</tr>" .
                "</tbody>";

        return $thead . $tbody;
    }

    public function makeTabellaReadOnlyIncasso($registrazione) {

        $bottoneVisualizzaRegistrazione = self::VISUALIZZA_INCASSO_HREF . $registrazione->getIdRegistrazione() . self::VISUALIZZA_ICON;

        $thead = "<thead>" .
                "  <tr>" .
                "      <th>%ml.datReg%</th>" .
                "      <th>%ml.descreg%</th>" .
                "      <th>%ml.negozio%</th>" .
                "      <th></th>" .
                "  </tr>" .
                "</thead>";

        $tbody = "<tbody>" .
                "  <tr>" .
                "      <td>" . date("d/m/Y", strtotime($registrazione->getDatRegistrazione())) . "</td>" .
                "      <td>" . trim($registrazione->getDesRegistrazione()) . "</td>" .
                "      <td>" . trim($registrazione->getCodNegozio()) . "</td>" .
                "      <td>" . $bottoneVisualizzaRegistrazione . "</td>" .
                "  </tr>" .
                "</tbody>";

        return $thead . $tbody;
    }

    public function makeTabellaRegistrazioneOriginale($registrazione) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $bottoneVisualizzaRegistrazione = self::MODIFICA_REGISTRAZIONE_HREF . $registrazione->getIdRegistrazione() . self::MODIFICA_ICON;

        $thead = "<thead>" .
                "	<tr>" .
                "		<th>%ml.datReg%</th>" .
                "		<th>%ml.descreg%</th>" .
                "		<th>%ml.negozio%</th>" .
                "		<th></th>" .
                "	</tr>" .
                "</thead>";

        $tbody = "<tbody>" .
                "	<tr>" .
                "		<td>" . date("d/m/Y", strtotime($registrazione->getDatRegistrazione())) . "</td>" .
                "		<td>" . trim($registrazione->getDesRegistrazione()) . "</td>" .
                "		<td>" . trim($registrazione->getCodNegozio()) . "</td>" .
                "		<td>" . $bottoneVisualizzaRegistrazione . "</td>" .
                "	</tr>" .
                "</tbody>";

        return $thead . $tbody;
    }

    public function makeTabellaPagamento($registrazione) {

        $bottoneModificaPagamento = self::MODIFICA_PAGAMENTO_HREF . $registrazione->getIdRegistrazione() . self::MODIFICA_ICON;

        $thead = "<thead>" .
                "   <tr>" .
                "       <th>%ml.datReg%</th>" .
                "	<th>%ml.descreg%</th>" .
                "	<th>%ml.negozio%</th>" .
                "	<th></th>" .
                "   </tr>" .
                "</thead>";

        $tbody = "<tbody>" .
                "   <tr>" .
                "       <td>" . date("d/m/Y", strtotime($registrazione->getDatRegistrazione())) . "</td>" .
                "       <td>" . trim($registrazione->getDesRegistrazione()) . "</td>" .
                "       <td>" . trim($registrazione->getCodNegozio()) . "</td>" .
                "       <td>" . $bottoneModificaPagamento . "</td>" .
                "   </tr>" .
                "</tbody>";

        return $thead . $tbody;
    }

    public function makeTabellaIncasso($registrazione) {

        $bottoneModificaIncasso = self::MODIFICA_INCASSO_HREF . $registrazione->getIdRegistrazione() . self::MODIFICA_ICON;

        $thead = "<thead>" .
                "  <tr>" .
                "      <th>%ml.datReg%</th>" .
                "      <th>%ml.descreg%</th>" .
                "      <th>%ml.negozio%</th>" .
                "      <th></th>" .
                "  </tr>" .
                "</thead>";

        $tbody = "<tbody>" .
                "  <tr>" .
                "      <td>" . date("d/m/Y", strtotime($registrazione->getDatRegistrazione())) . "</td>" .
                "      <td>" . trim($registrazione->getDesRegistrazione()) . "</td>" .
                "      <td>" . trim($registrazione->getCodNegozio()) . "</td>" .
                "      <td>" . $bottoneModificaIncasso . "</td>" .
                "  </tr>" .
                "</tbody>";

        return $thead . $tbody;
    }

    public function riga($dati): string {
        return "<tr>" .
                "   <td>" . $dati["descrizione"] . "</td>" .
                "   <td>" . $dati["data"] . "</td>" .
                "   <td>" . $dati["nota"] . "</td>" .
                "   <td>" . $dati["numfatt"] . "</td>" .
                "   <td>" . $dati["tipaddebito"] . "</td>" .
                "   <td " . $dati["tdclass"] . ">" . $dati["stascadenza"] . "</td>" .
                "   <td>" . number_format(trim($dati["importo"]), 2, ',', '.') . "</td>" .
                "   <td>" . $dati["bottoneVisualizzaScadenza"] . "</td>" .
                "   <td>" . $dati["bottoneModificaScadenza"] . "</td>" .
                "</tr>";
    }

    public function totaleData($dati): string {
        return "<tr>" .
                "   <td></td>" .
                "   <td></td>" .
                "   <td></td>" .
                "   <td></td>" .
                "   <td class='bg-info'><strong>" . $dati["labeltotaledata"] . "</strong></td>" .
                "   <td class='bg-info'></td>" .
                "   <td class='bg-info'><strong>" . number_format($dati["totaledata"], 2, ',', '.') . "</strong></td>" .
                "   <td class='bg-info'></td>" .
                "   <td class='bg-info'></td>" .
                "</tr>";
    }

    public function totaleCliFor($dati): string {
        return "<tr>" .
                "   <td>" . $dati["descrizione"] . "</td>" .
                "   <td>" . $dati["data"] . "</td>" .
                "   <td></td>" .
                "   <td></td>" .
                "   <td class='bg-info'><strong>" . $dati["labeltotaleclifor"] . "</strong></td>" .
                "   <td class='bg-info'></td>" .
                "   <td class='bg-info'><strong>" . number_format($dati["totaleclifor"], 2, ',', '.') . "</strong></td>" .
                "   <td class='bg-info'></td>" .
                "   <td class='bg-info'></td>" .
                "</tr>";
    }

    public function totaleScadenze($dati): string {
        return "<tr>" .
                "    <td>" . $dati["descrizione"] . "</td>" .
                "    <td>" . $dati["data"] . "</td>" .
                "    <td></td>" .
                "    <td></td>" .
                "    <td class='bg-info'><strong>" . $dati["labeltotalescadenze"] . "</strong></td>" .
                "    <td class='bg-info'></td>" .
                "    <td class='bg-info'><strong>" . number_format($dati["totalescadenze"], 2, ',', '.') . "</strong></td>" .
                "    <td class='bg-info'></td>" .
                "    <td class='bg-info'></td>" .
                "</tr>";
    }

    public function intestazione($dati): string {
        return "<div class='row'>" .
                "    <div class='col-sm-4'>" .
                "        <input class='form-control' id='myInput' type='text' placeholder='Ricerca in tabella...'>" .
                "    </div>" .
                "    <div class='col-sm-8'>" . $_SESSION[self::MSG] . "</div>" .
                "</div>" .
                "<br/>" .
                "<table class='table table-bordered table-hover'>" .
                "   <thead>" .
                "       <tr>" .
                "           <th>" . $dati["labelclifor"] . "</th>" .
                "           <th>" . $dati["labeldata"] . "</th>" .
                "           <th>" . $dati["labelnota"] . "</th>" .
                "           <th>" . $dati["labelnumfatt"] . "</th>" .
                "           <th>" . $dati["labeltipoaddebito"] . "</th>" .
                "           <th>" . $dati["labelstatoscadenza"] . "</th>" .
                "           <th>" . $dati["labelimporto"] . "</th>" .
                "           <th></th>" .
                "           <th></th>" .
                "       </tr>" .
                "   </thead>" .
                "   <tbody id='myTable'>";
    }

}

?>
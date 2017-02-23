<?php

class AndamentoNegoziController {

	private $andamentoNegozi;

	public function __construct(RiepiloghiBusinessInterface $andamentoNegozi) {
		$this->andamentoNegozi = $andamentoNegozi;
	}

	public function start() {
	
		if ($_REQUEST["modo"] == "start") {
			$this->andamentoNegozi->start();
		}
		if ($_REQUEST["modo"] == "go") {
			$_SESSION["datareg_da"] = $_REQUEST["datareg_da"];
			$_SESSION["datareg_a"] = $_REQUEST["datareg_a"];
			$_SESSION["codneg_sel"] = $_REQUEST["codneg_sel"];
			$this->andamentoNegozi->go();
		}
	}
}

?>
<?php
require_once 'nexus6.presentation.interface.php';

interface RiepiloghiPresentationInterface extends Nexus6PresentationInterface {
		
	public function tabellaTotaliRiepilogoNegozi($tipoTotale);
}

?>
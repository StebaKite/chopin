<?php
require_once 'nexus6.extractor.interface.php';

interface RiepiloghiExtractorInterface extends Nexus6ExtractorInterface {
	
	public function generaSezioneIntestazione($pdf);	
}

?>
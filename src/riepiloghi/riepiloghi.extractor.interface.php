<?php

require_once 'nexus6.main.interface.php';

interface RiepiloghiExtractorInterface extends MainNexus6Interface {

    public function generaSezioneIntestazione($pdf, $bilancio);
}

?>
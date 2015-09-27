UPDATE contabilita.scadenza
SET
	sta_scadenza='%sta_scadenza%',
	id_pagamento=%id_registrazione%
	
WHERE id_fornitore=%id_fornitore%
AND num_fattura = %num_fattura%
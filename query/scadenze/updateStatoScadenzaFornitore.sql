UPDATE contabilita.scadenza
SET
	sta_scadenza = '%sta_scadenza%',
	id_pagamento = '%id_pagamento%'
	
WHERE id_fornitore = '%id_fornitore%'
AND num_fattura = '%num_fattura%'
AND dat_scadenza = '%dat_scadenza%'
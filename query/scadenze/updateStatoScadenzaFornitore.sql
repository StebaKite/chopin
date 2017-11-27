UPDATE contabilita.scadenza
SET
	sta_scadenza = '%sta_scadenza%',
	id_pagamento = %id_pagamento%
	
WHERE id_scadenza = %id_scadenza%

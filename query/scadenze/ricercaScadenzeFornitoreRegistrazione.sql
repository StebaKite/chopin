SELECT 
	id_scadenza,
	to_char(dat_scadenza, 'DD/MM/YYYY') as dat_scadenza,
	imp_in_scadenza,
	nota_scadenza,
	tip_addebito,
	cod_negozio,
	id_fornitore,
	num_fattura,
	sta_scadenza,
	id_pagamento
FROM contabilita.scadenza
WHERE scadenza.id_registrazione = %id_registrazione%
ORDER BY dat_scadenza

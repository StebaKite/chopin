SELECT
	scadenza.id_scadenza,
	scadenza.id_registrazione,
	scadenza.dat_scadenza as dat_scadenza_yyyymmdd,
	to_char(scadenza.dat_scadenza, 'DD/MM/YYYY') as dat_scadenza,
	scadenza.dat_scadenza as dat_scadenza_originale,
	scadenza.imp_in_scadenza,
	scadenza.nota_scadenza,
	scadenza.tip_addebito,
	scadenza.num_fattura,
	scadenza.sta_scadenza,
	scadenza.id_pagamento
FROM contabilita.scadenza
WHERE id_scadenza = %id_scadenza%

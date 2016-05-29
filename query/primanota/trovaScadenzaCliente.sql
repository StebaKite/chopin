SELECT
	id_scadenza,
	sta_scadenza,
	id_incasso	
FROM contabilita.scadenza_cliente
WHERE id_registrazione 	= %id_registrazione%
AND   id_cliente       	= %id_cliente%
AND   dat_registrazione = %dat_registrazione%
AND   cod_negozio      	= %cod_negozio%
AND   num_fattura      	= %num_fattura%

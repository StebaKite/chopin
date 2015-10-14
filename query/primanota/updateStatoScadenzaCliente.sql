UPDATE contabilita.scadenza_cliente
SET
	sta_scadenza = '%sta_scadenza%',
	id_incasso = %id_registrazione%
	
WHERE id_cliente = %id_cliente%
AND num_fattura = %num_fattura%
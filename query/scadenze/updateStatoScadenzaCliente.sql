UPDATE contabilita.scadenza_cliente
SET
	sta_scadenza = '%sta_scadenza%',
	id_incasso = %id_incasso%
	
WHERE id_scadenza = %id_scadenza%

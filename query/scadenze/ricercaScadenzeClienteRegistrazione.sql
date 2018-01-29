SELECT 
	id_scadenza,
	to_char(dat_registrazione, 'DD-MM-YYYY') as dat_registrazione,
	imp_registrazione,
	nota,
	tip_addebito,
	cod_negozio,
	id_cliente,
	num_fattura,
	sta_scadenza,
	id_incasso
FROM contabilita.scadenza_cliente
WHERE scadenza_cliente.id_registrazione = %id_registrazione%
ORDER BY dat_registrazione

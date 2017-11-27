SELECT
	scadenza_cliente.id_scadenza,
	scadenza_cliente.id_registrazione,
	scadenza_cliente.dat_registrazione as dat_registrazione_yyyymmdd,
	to_char(scadenza_cliente.dat_registrazione, 'DD/MM/YYYY') as dat_registrazione,
	scadenza_cliente.dat_registrazione as dat_registrazione_originale,
	scadenza_cliente.imp_registrazione,
	scadenza_cliente.nota,
	scadenza_cliente.tip_addebito,
	scadenza_cliente.num_fattura,
	scadenza_cliente.sta_scadenza,
	scadenza_cliente.id_incasso
FROM contabilita.scadenza_cliente
WHERE id_scadenza = %id_scadenza%

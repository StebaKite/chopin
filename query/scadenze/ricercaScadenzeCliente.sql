SELECT
	scadenza_cliente.id_scadenza,
	scadenza_cliente.id_registrazione,
	registrazione.id_cliente,
	registrazione.sta_registrazione,
	cliente.des_cliente,
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
	INNER JOIN contabilita.registrazione
		ON registrazione.id_registrazione = scadenza_cliente.id_registrazione
	INNER JOIN contabilita.cliente
		ON cliente.id_cliente = registrazione.id_cliente
WHERE 1 = 1
%filtro_date%
ORDER BY dat_registrazione_originale, registrazione.id_cliente, scadenza_cliente.num_fattura

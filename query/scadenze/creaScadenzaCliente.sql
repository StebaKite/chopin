INSERT INTO contabilita.scadenza_cliente
	(id_scadenza,
	id_registrazione,
	dat_registrazione,
	imp_registrazione,
	nota,
	tip_addebito,
	cod_negozio,
	id_cliente,
	num_fattura,
	sta_scadenza,
	id_incasso)
VALUES
	(nextval('contabilita.scadenza_cliente_id_scadenza_seq'),
	%id_registrazione%,
	'%dat_registrazione%',
	%imp_registrazione%,
	%nota%,
	%tip_addebito%,
	%cod_negozio%,
	%id_cliente%,
	%num_fattura%,
	%sta_scadenza%,
	null
	)
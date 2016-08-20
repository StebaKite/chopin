INSERT INTO contabilita.registrazione
VALUES
	(nextval('contabilita.registrazione_id_registrazione_seq'),
	%dat_scadenza%,
	'%des_registrazione%',
	%id_fornitore%,
	%id_cliente%,
	'%cod_causale%',
	%num_fattura%,
	%dat_registrazione%,
	'%dat_inserimento%',
	'%sta_registrazione%',
	%cod_negozio%,
	%id_mercato%
	) RETURNING id_registrazione

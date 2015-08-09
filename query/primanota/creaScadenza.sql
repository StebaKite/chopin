INSERT INTO contabilita.scadenza
VALUES
	(nextval('contabilita.scadenza_id_scadenza_seq'),
	%id_registrazione%,
	%dat_scadenza%,
	%imp_in_scadenza%,
	'%nota_in_scadenza%'
	)

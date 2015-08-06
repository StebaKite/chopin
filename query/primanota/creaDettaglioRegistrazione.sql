INSERT INTO contabilita.dettaglio_registrazione
VALUES
	(nextval('contabilita.dettaglio_registrazione_id_dettaglio_registrazione_seq'),
	%id_registrazione%,
	%imp_registrazione%,
	'%ind_dareavere%',
	'%cod_conto%',
	'%cod_sottoconto%'
	) RETURNING id_dettaglio_registrazione

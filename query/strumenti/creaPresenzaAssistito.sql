INSERT INTO contabilita.presenza_assistito
VALUES
	(nextval('contabilita.presenza_assistito_id_presenza_seq'),
	'%dat_presenza%',
	%id_assistito%,
        '%cod_negozio%'
	) RETURNING id_presenza

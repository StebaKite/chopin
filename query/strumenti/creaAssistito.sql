INSERT INTO contabilita.assistito
VALUES
	(nextval('contabilita.assistito_id_assistito_seq'),
	'%des_assistito%'
	) RETURNING id_assistito

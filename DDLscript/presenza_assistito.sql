-- Drop table

-- DROP TABLE contabilita.presenza_assistito

CREATE TABLE contabilita.presenza_assistito (
	id_presenza serial NOT NULL,
	dat_presenza date NOT NULL,
	id_assistito int4 NOT NULL,
	cod_negozio bpchar(3) NOT NULL,
	CONSTRAINT newtable_1_pk PRIMARY KEY (id_presenza),
	CONSTRAINT presenza_assistito_assistito_fk FOREIGN KEY (id_assistito) REFERENCES contabilita.assistito(id_assistito) ON DELETE CASCADE
)
WITH (
	OIDS=FALSE
) ;

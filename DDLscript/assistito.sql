-- Drop table

-- DROP TABLE contabilita.assistito

CREATE TABLE contabilita.assistito (
	id_assistito serial NOT NULL,
	des_assistito varchar(100) NOT NULL,
	dat_inserimento timestamp NOT NULL DEFAULT now(),
	CONSTRAINT assistito_pk PRIMARY KEY (id_assistito)
)
WITH (
	OIDS=FALSE
) ;

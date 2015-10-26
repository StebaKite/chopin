-- Table: contabilita.lavoro_pianificato

DROP TABLE contabilita.lavoro_pianificato;

CREATE TABLE contabilita.lavoro_pianificato
(
  pk_lavoro_pianificato serial NOT NULL,
  dat_lavoro date NOT NULL,
  des_lavoro character varying(200) NOT NULL,
  cla_esecuzione_lavoro character(100) NOT NULL,
  sta_lavoro character(2),
  CONSTRAINT pk_lavoro PRIMARY KEY (pk_lavoro_pianificato)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE contabilita.lavoro_pianificato
  OWNER TO postgres;

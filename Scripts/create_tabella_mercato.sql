-- Table: contabilita.mercato

-- DROP TABLE contabilita.mercato;

CREATE TABLE contabilita.mercato
(
  id_mercato integer NOT NULL DEFAULT nextval('contabilita.mercato_id_mercato_seq'::regclass),
  cod_mercato character(10) DEFAULT NULL::bpchar,
  des_mercato character varying(200) DEFAULT NULL::character varying,
  citta_mercato character varying(100) DEFAULT NULL::character varying,
  cod_negozio character(3) DEFAULT NULL::bpchar,
  CONSTRAINT "pk-mercato" PRIMARY KEY (id_mercato)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE contabilita.mercato
  OWNER TO postgres;
COMMENT ON TABLE contabilita.mercato
  IS 'Anagrafica mercati';

-- Foreign Key   
  
ALTER TABLE contabilita.registrazione
  ADD CONSTRAINT mercato_registrazione_fk FOREIGN KEY (id_mercato) REFERENCES contabilita.mercato (id_mercato)
   ON UPDATE NO ACTION ON DELETE SET NULL;
CREATE INDEX fki_mercato_registrazione_fk
  ON contabilita.registrazione(id_mercato);
  
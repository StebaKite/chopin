-- Table: contabilita.progressivo_fattura

-- DROP TABLE contabilita.progressivo_fattura;

CREATE TABLE contabilita.progressivo_fattura
(
  cat_cliente character(4) NOT NULL,
  neg_progr character(3) NOT NULL,
  num_fattura_ultimo smallint,
  CONSTRAINT pk_progr_fattura PRIMARY KEY (cat_cliente, neg_progr),
  CONSTRAINT fk_cat_cliente FOREIGN KEY (cat_cliente)
      REFERENCES contabilita.categoria_cliente (cat_cliente) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);
ALTER TABLE contabilita.progressivo_fattura
  OWNER TO postgres;

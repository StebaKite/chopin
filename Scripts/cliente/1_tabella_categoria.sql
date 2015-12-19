-- Table: contabilita.categoria_cliente

-- DROP TABLE contabilita.categoria_cliente;

CREATE TABLE contabilita.categoria_cliente
(
  cat_cliente character(4) NOT NULL,
  des_categoria character(100) NOT NULL,
  num_fattura_ultimo smallint NOT NULL DEFAULT 0,
  CONSTRAINT pk_categoria_cliente PRIMARY KEY (cat_cliente)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE contabilita.categoria_cliente
  OWNER TO postgres;

-- Popolo tabella con le quattro categorie attuali  
  
INSERT INTO contabilita.categoria_cliente(
            cat_cliente, des_categoria, num_fattura_ultimo)
    VALUES ('1000', 'Famiglia', 0);

INSERT INTO contabilita.categoria_cliente(
            cat_cliente, des_categoria, num_fattura_ultimo)
    VALUES ('1100', 'Cliente Vendita Prodotti', 0);

INSERT INTO contabilita.categoria_cliente(
            cat_cliente, des_categoria, num_fattura_ultimo)
    VALUES ('1200', 'Azienda Consortile', 0);

INSERT INTO contabilita.categoria_cliente(
            cat_cliente, des_categoria, num_fattura_ultimo)
    VALUES ('1300', 'Ente Pubblico', 0);
  
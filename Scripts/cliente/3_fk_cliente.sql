   
-- Foreign Key: contabilita.fk_cliente_categoria

-- ALTER TABLE contabilita.cliente DROP CONSTRAINT fk_cliente_categoria;

ALTER TABLE contabilita.cliente
  ADD CONSTRAINT fk_cliente_categoria FOREIGN KEY (cat_cliente)
      REFERENCES contabilita.categoria_cliente (cat_cliente) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION;

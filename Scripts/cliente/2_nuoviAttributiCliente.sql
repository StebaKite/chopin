

ALTER TABLE contabilita.cliente
   ADD COLUMN cod_piva character(11);
   
ALTER TABLE contabilita.cliente
   ADD COLUMN cod_fisc character(16);
   
ALTER TABLE contabilita.cliente
   ADD COLUMN cat_cliente character(30);
COMMENT ON COLUMN contabilita.cliente.cat_cliente
  IS 'Categoria cliente';
  
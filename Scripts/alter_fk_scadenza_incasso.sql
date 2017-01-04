-- Foreign Key: contabilita.pagamento_scadenza_cliente_fk

ALTER TABLE contabilita.scadenza_cliente DROP CONSTRAINT pagamento_scadenza_cliente_fk;

ALTER TABLE contabilita.scadenza_cliente
  ADD CONSTRAINT incasso_scadenza_cliente_fk FOREIGN KEY (id_incasso)
      REFERENCES contabilita.registrazione (id_registrazione) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE SET NULL;
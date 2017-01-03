-- Foreign Key: contabilita.pagamento_scadenza_fk

ALTER TABLE contabilita.scadenza DROP CONSTRAINT pagamento_scadenza_fk;

ALTER TABLE contabilita.scadenza
  ADD CONSTRAINT pagamento_scadenza_fk FOREIGN KEY (id_pagamento)
      REFERENCES contabilita.registrazione (id_registrazione) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE SET NULL;
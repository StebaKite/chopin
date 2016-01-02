ALTER TABLE contabilita.causale
   ADD COLUMN cat_causale character(6);
COMMENT ON COLUMN contabilita.causale.cat_causale
  IS 'Categoria causale: incpag, corrisp, altre';

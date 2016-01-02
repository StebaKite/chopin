UPDATE contabilita.causale
SET des_causale='%des_causale%',
	dat_inserimento=now(),
	cat_causale='%cat_causale%'
WHERE cod_causale='%cod_causale%'

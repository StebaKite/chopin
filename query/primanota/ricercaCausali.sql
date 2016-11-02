SELECT
	causale.cod_causale,
	causale.des_causale,
	causale.dat_inserimento	
FROM contabilita.causale as causale
WHERE cat_causale like '%cat_causale%'
ORDER BY causale.des_causale
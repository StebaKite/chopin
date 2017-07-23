SELECT
	causale.cod_causale,
	causale.des_causale,
	causale.dat_inserimento	
FROM contabilita.causale as causale
ORDER BY causale.des_causale
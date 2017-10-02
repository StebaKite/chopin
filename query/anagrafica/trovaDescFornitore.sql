SELECT 
	id_fornitore, 
	cod_fornitore, 
	num_gg_scadenza_fattura, 
	tip_addebito
FROM contabilita.fornitore
WHERE des_fornitore = '%des_fornitore%'
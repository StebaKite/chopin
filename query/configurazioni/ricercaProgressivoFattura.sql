SELECT
	cat_cliente, 
	neg_progr, 
	num_fattura_ultimo, 
	nota_testa_fattura, 
	nota_piede_fattura
	
FROM contabilita.progressivo_fattura

WHERE 1 = 1 %filtri_progressivi_fattura%

ORDER BY neg_progr

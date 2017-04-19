SELECT
	categoria.cat_cliente, 
	categoria.des_categoria, 
	progressivo.neg_progr, 
	progressivo.num_fattura_ultimo, 
	progressivo.nota_testa_fattura, 
	progressivo.nota_piede_fattura	
FROM contabilita.progressivo_fattura AS progressivo
	INNER JOIN contabilita.categoria_cliente AS categoria
		ON categoria.cat_cliente = progressivo.cat_cliente
ORDER BY neg_progr

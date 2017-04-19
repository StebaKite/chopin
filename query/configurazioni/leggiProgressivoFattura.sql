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
WHERE progressivo.cat_cliente = '%cat_cliente%'
AND   progressivo.neg_progr = '%neg_progr%'
ORDER BY progressivo.neg_progr

SELECT progressivo_fattura.num_fattura_ultimo
FROM contabilita.categoria_cliente
	INNER JOIN contabilita.progressivo_fattura
		ON progressivo_fattura.cat_cliente = categoria_cliente.cat_cliente
WHERE categoria_cliente.cat_cliente = '%cat_cliente%'
  AND progressivo_fattura.neg_progr = '%neg_progr%'
SELECT
	id_cliente,
	cod_cliente,
	des_cliente
FROM contabilita.cliente
WHERE cat_cliente = '%cat_cliente%'
ORDER BY des_cliente

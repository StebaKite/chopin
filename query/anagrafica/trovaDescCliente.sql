SELECT 
	id_cliente, 
	cod_cliente, 
	tip_addebito
FROM contabilita.cliente
WHERE des_cliente = '%des_cliente%'

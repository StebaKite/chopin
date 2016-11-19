SELECT id_cliente, des_cliente 
FROM contabilita.cliente
WHERE cod_piva = '%cod_piva%'
AND id_cliente != '%id_cliente%'
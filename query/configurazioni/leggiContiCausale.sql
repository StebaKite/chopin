SELECT 
	config.cod_conto, 
	conto.des_conto
FROM contabilita.configurazione_causale as config
	INNER JOIN contabilita.conto as conto
		ON conto.cod_conto = config.cod_conto
WHERE cod_causale = '%cod_causale%'
ORDER BY config.cod_conto

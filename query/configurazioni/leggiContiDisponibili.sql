SELECT 
	config.cod_conto, 
	conto.des_conto
FROM contabilita.configurazione_causale as config
	INNER JOIN contabilita.conto as conto
		ON conto.cod_conto = config.cod_conto
WHERE NOT EXISTS
	(SELECT cod_conto
	   FROM contabilita.configurazione_causale as config2
	  WHERE config2.cod_causale = '%cod_causale%'
	    AND config2.cod_conto = config.cod_conto 
	  )
ORDER BY config.cod_conto

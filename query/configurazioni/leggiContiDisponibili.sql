SELECT DISTINCT
	conto.cod_conto, 
	conto.des_conto
FROM contabilita.conto as conto
	LEFT OUTER JOIN  contabilita.configurazione_causale as config
		ON config.cod_conto = conto.cod_conto
WHERE NOT EXISTS (
	SELECT cod_conto
	   FROM contabilita.configurazione_causale as config2
	  WHERE config2.cod_causale = '%cod_causale%'
	    AND config2.cod_conto = config.cod_conto 
	  )
ORDER BY conto.cod_conto
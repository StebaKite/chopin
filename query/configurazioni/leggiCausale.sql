SELECT 
	t3.*,
	coalesce(t1.totalereg, 0) AS tot_registrazioni_causale,
	coalesce(t2.totaleconti, 0) AS tot_conti_causale	
  FROM (
	SELECT cod_causale, des_causale, dat_inserimento, cat_causale
	FROM contabilita.causale
	WHERE cod_causale = '%cod_causale%'  
  ) AS t3
	LEFT OUTER JOIN
		(SELECT	cod_causale, count(*) AS totalereg
		   FROM contabilita.registrazione
		   GROUP BY cod_causale
		) AS t1
	  ON t1.cod_causale = t3.cod_causale 	
	LEFT OUTER JOIN
		(SELECT	cod_causale, count(*) AS totaleconti
		   FROM contabilita.configurazione_causale
		   GROUP BY cod_causale
		) AS t2
	  ON t2.cod_causale = t3.cod_causale 	

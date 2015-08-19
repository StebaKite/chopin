SELECT
	causale.cod_causale,
	causale.des_causale,
	causale.dat_inserimento,
	coalesce(t1.totalereg, 0) AS tot_registrazioni_causale,
	coalesce(t2.totaleconti, 0) AS tot_conti_causale
FROM contabilita.causale as causale
	LEFT OUTER JOIN
		(SELECT	cod_causale, count(*) AS totalereg
		   FROM contabilita.registrazione
		   GROUP BY cod_causale
		) AS t1
	  ON t1.cod_causale = causale.cod_causale 	
	LEFT OUTER JOIN
		(SELECT	cod_causale, count(*) AS totaleconti
		   FROM contabilita.configurazione_causale
		   GROUP BY cod_causale
		) AS t2
	  ON t2.cod_causale = causale.cod_causale 	
%cod_causale%
ORDER BY causale.cod_causale

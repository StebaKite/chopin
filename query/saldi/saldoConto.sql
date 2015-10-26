SELECT
	t4.cod_conto,
	t4.cod_sottoconto,
	t4.tip_conto,
	COALESCE(sum(t4.tot_conto * t4.tip_conto),0) as tot_conto
  FROM (	
		SELECT
				t3.cod_conto,
				t1.cod_sottoconto,
				CASE 
					WHEN t3.tip_conto = 'Dare' then 1
					WHEN t3.tip_conto = 'Avere' then -1
				END AS tip_conto,	
				SUM(t2.imp_registrazione) as tot_conto
		  FROM contabilita.sottoconto as t1
				INNER JOIN contabilita.conto as t3
					ON t3.cod_conto = t1.cod_conto 
		  		LEFT OUTER JOIN contabilita.dettaglio_registrazione as t2
		  			ON  t2.cod_conto = t1.cod_conto
		  	   		AND t2.cod_sottoconto = t1.cod_sottoconto
		  		LEFT OUTER JOIN contabilita.registrazione as t4
		  			ON  t4.id_registrazione = t2.id_registrazione
		  WHERE t4.dat_registrazione between '%datareg_da%' and '%datareg_a%'
		  AND   t4.cod_negozio = '%codnegozio%'
		  AND   t3.cod_conto = '%codconto%'
		  AND   t1.cod_sottoconto = '%codsottoconto%'
		GROUP BY t3.cod_conto, t1.cod_sottoconto, tip_conto
	) AS t4	
GROUP BY t4.cod_conto, t4.cod_sottoconto, t4.tip_conto
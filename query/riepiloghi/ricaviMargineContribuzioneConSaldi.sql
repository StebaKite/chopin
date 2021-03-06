SELECT
	COALESCE(sum(t4.tot_conto * t4.ind_dareavere),0) as totalericavovendita
  FROM (
  		SELECT 
				CASE 
					WHEN t2.ind_dareavere = 'D' then  1
					WHEN t2.ind_dareavere = 'A' then -1
				END AS ind_dareavere,	
				SUM(t2.imp_registrazione) as tot_conto		  
				
 		FROM (
  			 SELECT sottoconto.*
			   FROM contabilita.sottoconto
		  	  WHERE  sottoconto.ind_gruppo = 'RC'
 			) as t1
 		
			INNER JOIN contabilita.conto as t3
				ON t3.cod_conto = t1.cod_conto 
				
	  		LEFT OUTER JOIN contabilita.dettaglio_registrazione as t2
	  			ON  t2.cod_conto = t1.cod_conto
	  	   		AND t2.cod_sottoconto = t1.cod_sottoconto
	  	   		
	  		LEFT OUTER JOIN contabilita.registrazione as t4
	  			ON  t4.id_registrazione = t2.id_registrazione
	  			
		WHERE t4.dat_registrazione BETWEEN '%datareg_da%' AND '%datareg_a%'
		AND   t4.cod_negozio IN (%codnegozio%)
		AND   t3.cat_conto = 'Conto Economico'
		AND   t3.tip_conto = 'Avere'	  
		GROUP BY t2.ind_dareavere
		
		UNION ALL
		
		SELECT
			CASE 
				WHEN saldo.ind_dareavere = 'D' then  1
				WHEN saldo.ind_dareavere = 'A' then -1
			END AS ind_dareavere,
			saldo.imp_saldo as tot_conto
		 FROM contabilita.saldo  
			INNER JOIN contabilita.conto as conto
			  ON conto.cod_conto = saldo.cod_conto
			INNER JOIN (
	 			 SELECT sottoconto.*
				   FROM contabilita.sottoconto
			  	  WHERE  sottoconto.ind_gruppo = 'RC'
	 			) as sottoconto
			  ON sottoconto.cod_conto = saldo.cod_conto	  
			  AND sottoconto.cod_sottoconto = saldo.cod_sottoconto	  
		 WHERE saldo.dat_saldo = '%datareg_da%'
		  AND saldo.cod_negozio IN (%codnegozio%)		  
		  AND conto.cat_conto = 'Conto Economico'
		  AND conto.ind_presenza_in_bilancio = 'S'
		
) AS t4	
   
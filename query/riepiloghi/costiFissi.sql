SELECT
	COALESCE(sum(t4.tot_conto * t4.ind_dareavere),0) as totalecostofisso
  FROM (
  		SELECT 
				CASE 
					WHEN t2.ind_dareavere = 'D' then  1
					WHEN t2.ind_dareavere = 'A' then -1
				END AS ind_dareavere,	
				SUM(t2.imp_registrazione) as tot_conto		  
				
 		FROM 
 			(
				SELECT sottoconto.*
				  FROM (
					    SELECT *, cod_conto||cod_sottoconto as conto_sottoconto
					      FROM contabilita.sottoconto	  			
	  			       ) as sottoconto
				WHERE sottoconto.conto_sottoconto not in 
					('30010','30020','30030','30040','30050','30060','30065',
					 '320101','320201','320301','320401','320501','320601',
					 '32510','32520',
					 '33090'
					)
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
	  AND   t3.tip_conto = 'Dare'
	  
	GROUP BY t2.ind_dareavere
) AS t4
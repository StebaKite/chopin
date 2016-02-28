SELECT
	t5.num_riga_bilancio,
	t5.des_conto,
	t5.cod_negozio,
	coalesce(sum(t5.tot_conto),0) as tot_conto
  FROM (
		SELECT
			t4.num_riga_bilancio,
			t4.des_conto,
			t4.cod_negozio,
			COALESCE(sum(t4.tot_conto * t4.ind_dareavere),0) as tot_conto
		  FROM (
		  		SELECT 
						t3.num_riga_bilancio,
						t3.des_conto,
						CASE 
							WHEN t2.ind_dareavere = 'D' then  1
							WHEN t2.ind_dareavere = 'A' then -1
						END AS ind_dareavere,	
						t4.cod_negozio,
						SUM(t2.imp_registrazione) as tot_conto		  
		 		FROM contabilita.sottoconto as t1
					INNER JOIN contabilita.conto as t3
						ON t3.cod_conto = t1.cod_conto 
			  		LEFT OUTER JOIN contabilita.dettaglio_registrazione as t2
			  			ON  t2.cod_conto = t1.cod_conto
			  	   		AND t2.cod_sottoconto = t1.cod_sottoconto
			  		LEFT OUTER JOIN contabilita.registrazione as t4
			  			ON  t4.id_registrazione = t2.id_registrazione
			  WHERE t4.dat_registrazione BETWEEN '%datareg_da%' AND '%datareg_a%'
			  AND   t3.cat_conto = 'Conto Economico'			  
			  AND   t3.ind_presenza_in_bilancio = 'S'
			  AND   t3.tip_conto = 'Dare' 
			GROUP BY t3.num_riga_bilancio, t3.des_conto, t2.ind_dareavere, t4.cod_negozio
		) AS t4	
	GROUP BY t4.num_riga_bilancio, t4.des_conto, t4.cod_negozio
	) t5 
GROUP BY t5.num_riga_bilancio, t5.des_conto, t5.cod_negozio	
ORDER BY t5.num_riga_bilancio, t5.des_conto, t5.cod_negozio
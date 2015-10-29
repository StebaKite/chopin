SELECT
	t5.cod_conto,
	t5.cod_sottoconto,
	t5.tip_conto,
	COALESCE(sum(t5.tot_conto * t5.tip_conto),0) as tot_conto
  FROM (
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
		
		UNION ALL

		SELECT
			saldo.cod_conto,
			saldo.cod_sottoconto,
			CASE 
				WHEN saldo.ind_dareavere = 'D' then 1
				WHEN saldo.ind_dareavere = 'A' then -1
			END AS tip_conto,
			saldo.imp_saldo as tot_conto
		 FROM contabilita.saldo  
		 WHERE saldo.dat_saldo = '%datareg_da%'
		  AND saldo.cod_negozio = '%codnegozio%'		  
		  AND saldo.cod_conto = '%codconto%'
		  AND saldo.cod_sottoconto = '%codsottoconto%'
		) t5 
	GROUP BY t5.cod_conto, t5.cod_sottoconto, t5.tip_conto	
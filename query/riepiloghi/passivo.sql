SELECT
	t5.num_riga_bilancio,
	t5.ind_visibilita_sottoconti,
	t5.des_conto,
	t5.des_sottoconto,
	coalesce(sum(t5.tot_conto),0) as tot_conto
  FROM (
		SELECT
			t4.num_riga_bilancio,
			t4.ind_visibilita_sottoconti,
			t4.des_conto,
			t4.des_sottoconto,
			COALESCE(sum(t4.tot_conto * t4.ind_dareavere),0) as tot_conto
		  FROM (	
				SELECT
						t3.num_riga_bilancio,
						t3.ind_visibilita_sottoconti,
						t3.des_conto,
						t1.des_sottoconto,
						CASE 
							WHEN t2.ind_dareavere = 'D' then  1
							WHEN t2.ind_dareavere = 'A' then -1
						END AS ind_dareavere,	
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
				  AND   t4.cod_negozio = '%codnegozio%'
				  AND   t3.cat_conto = '%catconto%'
				  AND   t3.ind_presenza_in_bilancio = 'S'
				  AND   t3.ind_classificazione_conto = 'P'
				GROUP BY t3.num_riga_bilancio, t3.ind_visibilita_sottoconti, t3.des_conto, t1.des_sottoconto, t2.ind_dareavere
			) AS t4	
		GROUP BY t4.num_riga_bilancio, t4.ind_visibilita_sottoconti, t4.des_conto, t4.des_sottoconto

		UNION ALL
		
		SELECT
			conto.num_riga_bilancio,
			conto.ind_visibilita_sottoconti,
			conto.des_conto,
			sottoconto.des_sottoconto,
			saldo.imp_saldo as tot_conto
		 FROM contabilita.saldo  
			INNER JOIN contabilita.conto as conto
			  ON conto.cod_conto = saldo.cod_conto
			INNER JOIN contabilita.sottoconto as sottoconto
			  ON sottoconto.cod_conto = saldo.cod_conto	  
			  AND sottoconto.cod_sottoconto = saldo.cod_sottoconto	  
		 WHERE saldo.dat_saldo = '%datareg_da%'
		  AND saldo.cod_negozio = '%codnegozio%'		  
		  AND saldo.ind_dareavere = 'A' 		  
		  AND conto.cat_conto = '%catconto%'
		  AND conto.ind_presenza_in_bilancio = 'S'
	) t5 
GROUP BY t5.num_riga_bilancio, t5.ind_visibilita_sottoconti, t5.des_conto, t5.des_sottoconto	
ORDER BY t5.num_riga_bilancio, t5.des_conto, t5.des_sottoconto	
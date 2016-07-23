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
			t4.tip_conto,
			COALESCE(sum(t4.tot_conto * t4.ind_dareavere),0) as tot_conto
		  FROM (	
				SELECT
					t3.num_riga_bilancio,
					t3.ind_visibilita_sottoconti,
					t3.des_conto,
					t1.des_sottoconto,
					CASE 
						WHEN t2.ind_dareavere = 'D' then 1
						WHEN t2.ind_dareavere = 'A' then -1
					END AS ind_dareavere,
					t3.tip_conto,
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
				  AND   t4.cod_negozio IN (%codnegozio%)
				  AND   t3.cat_conto = 'Stato Patrimoniale'
				  AND   t3.ind_presenza_in_bilancio = 'S'
				GROUP BY t3.num_riga_bilancio, t3.ind_visibilita_sottoconti, t3.des_conto, t1.des_sottoconto, t2.ind_dareavere, t3.tip_conto
			) AS t4	
		GROUP BY t4.num_riga_bilancio, t4.ind_visibilita_sottoconti, t4.des_conto, t4.des_sottoconto, t4.tip_conto

		UNION ALL
		
		SELECT 
			t6.num_riga_bilancio,
			t6.ind_visibilita_sottoconti,
			t6.des_conto,
			t6.des_sottoconto,
			t6.tip_conto,
			COALESCE(SUM(t6.imp_saldo * t6.ind_dareavere),0) as tot_conto
		  FROM (
				SELECT
					conto.num_riga_bilancio,
					conto.ind_visibilita_sottoconti,
					conto.des_conto,
					conto.tip_conto,
					sottoconto.des_sottoconto,
					saldo.imp_saldo,
					CASE 
						WHEN saldo.ind_dareavere = 'D' then 1
						WHEN saldo.ind_dareavere = 'A' then -1
					END AS ind_dareavere
				 FROM contabilita.saldo  
					INNER JOIN contabilita.conto as conto
					  ON conto.cod_conto = saldo.cod_conto
					INNER JOIN contabilita.sottoconto as sottoconto
					  ON sottoconto.cod_conto = saldo.cod_conto	  
					  AND sottoconto.cod_sottoconto = saldo.cod_sottoconto	  
				 WHERE saldo.dat_saldo = '%datareg_da%'
				  AND saldo.cod_negozio IN (%codnegozio%)		  
				  AND conto.cat_conto = 'Stato Patrimoniale'
				  AND conto.ind_presenza_in_bilancio = 'S'
				) as t6
			GROUP BY t6.num_riga_bilancio, t6.ind_visibilita_sottoconti, t6.des_conto, t6.des_sottoconto, t6.tip_conto
	) t5 
WHERE t5.tip_conto = 'Dare'	
GROUP BY t5.num_riga_bilancio, t5.ind_visibilita_sottoconti, t5.des_conto, t5.des_sottoconto	
ORDER BY t5.num_riga_bilancio, t5.des_conto, t5.des_sottoconto		
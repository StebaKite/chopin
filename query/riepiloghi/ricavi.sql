SELECT
	t5.num_riga_bilancio,
	t5.ind_visibilita_sottoconti,
	t5.des_conto,
	t5.des_sottoconto,
	COALESCE(SUM(t5.tot_conto),0) as tot_conto
  FROM (
		SELECT
			t6.num_riga_bilancio,
			t6.ind_visibilita_sottoconti,
			t6.des_conto,
			t6.des_sottoconto,
			COALESCE(SUM(t6.tot_conto * t6.ind_dareavere),0) as tot_conto
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
			  AND   t4.cod_negozio IN (%codnegozio%)
			  AND   t3.cat_conto = 'Conto Economico'
			  AND   t3.ind_presenza_in_bilancio = 'S'
			  AND   t3.tip_conto = 'Avere' 
			GROUP BY t3.num_riga_bilancio, t3.ind_visibilita_sottoconti, t3.des_conto, t1.des_sottoconto, t2.ind_dareavere
			) AS t6	
		GROUP BY t6.num_riga_bilancio, t6.ind_visibilita_sottoconti, t6.des_conto, t6.des_sottoconto
	) t5 
GROUP BY t5.num_riga_bilancio, t5.ind_visibilita_sottoconti, t5.des_conto, t5.des_sottoconto	
ORDER BY t5.num_riga_bilancio, t5.des_conto, t5.des_sottoconto
select
	t4.num_riga_bilancio,
	t4.ind_visibilita_sottoconti,
	t4.des_conto,
	t4.des_sottoconto,
	coalesce(sum(t4.tot_conto * t4.ind_dareavere),0) as tot_conto
  from (	
		select
				t3.num_riga_bilancio,
				t3.ind_visibilita_sottoconti,
				t3.des_conto,
				t1.des_sottoconto,
				case 
					when t2.ind_dareavere = 'D' then -1
					when t2.ind_dareavere = 'A' then  1
				end as ind_dareavere,	
				sum(t2.imp_registrazione) as tot_conto
		  from contabilita.sottoconto as t1
				inner join contabilita.conto as t3
					on t3.cod_conto = t1.cod_conto 
		  		left outer join contabilita.dettaglio_registrazione as t2
		  			on  t2.cod_conto = t1.cod_conto
		  	   		and t2.cod_sottoconto = t1.cod_sottoconto
		  		left outer join contabilita.registrazione as t4
		  			on  t4.id_registrazione = t2.id_registrazione
		  where t4.dat_registrazione between '%datareg_da%' and '%datareg_a%'
		  and   t4.cod_negozio = '%codnegozio%'
		  and   t3.cat_conto = '%catconto%'
		  and   t3.ind_presenza_in_bilancio = 'S'
		  and   t3.ind_classificazione_conto = 'A'
		group by t3.num_riga_bilancio, t3.ind_visibilita_sottoconti, t3.des_conto, t1.des_sottoconto, t2.ind_dareavere
		order by t3.num_riga_bilancio, t3.des_conto, t1.des_sottoconto
	) as t4	
group by t4.num_riga_bilancio, t4.ind_visibilita_sottoconti, t4.des_conto, t4.des_sottoconto
order by t4.num_riga_bilancio, t4.des_conto, t4.des_sottoconto
	
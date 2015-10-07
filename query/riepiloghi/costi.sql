select
	t3.des_conto,
	t1.des_sottoconto,
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
  and   t2.ind_dareavere = 'D' 
group by t3.des_conto, t1.des_sottoconto
order by t3.des_conto, t1.des_sottoconto
select
	t3.des_conto,
	t2.ind_dareavere,
	sum(t2.imp_registrazione)
  from contabilita.sottoconto as t1
		inner join contabilita.conto as t3
			on t3.cod_conto = t1.cod_conto 
  		inner join contabilita.dettaglio_registrazione as t2
  			on  t2.cod_conto = t1.cod_conto
  	   		and t2.cod_sottoconto = t1.cod_sottoconto
  where t3.cat_conto = 'Conto Economico'		
group by t3.des_conto, t2.ind_dareavere
order by 1
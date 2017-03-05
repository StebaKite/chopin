select 
	t2.des_mercato || ' - ' || t2.citta_mercato as des_mercato,
	extract(month from t1.dat_registrazione) as mm_registrazione,
	coalesce(sum(t3.imp_registrazione),0) as imp_ricavo_mercato
  from contabilita.registrazione as t1
  	inner join 
  		(select id_mercato, cod_mercato, des_mercato, citta_mercato
  			from contabilita.mercato
  			where cod_negozio = 'VIL'  		
  		) as t2
  		on t2.id_mercato = t1.id_mercato
	inner JOIN contabilita.dettaglio_registrazione as t3
		on t3.id_registrazione = t1.id_registrazione
		and t3.ind_dareavere = 'A'
 where t1.dat_registrazione BETWEEN '01-01-2017' AND '31-12-2017'	
 and   t3.cod_conto = '400'
 and   t3.cod_sottoconto = '20'	
 group by mm_registrazione, t2.des_mercato, t2.citta_mercato 		
 order by mm_registrazione
  		
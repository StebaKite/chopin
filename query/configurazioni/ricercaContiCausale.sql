select 
	sottoconto.cod_conto,
	sottoconto.cod_sottoconto,
	sottoconto.des_sottoconto
  from contabilita.sottoconto as sottoconto
  	inner join
  		(select
  			cod_causale,
  			cod_conto
  		   from contabilita.configurazione_causale
  		   where cod_causale = '%cod_causale%'
  		) as t1
  		on t1.cod_conto = sottoconto.cod_conto
  order by sottoconto.des_sottoconto
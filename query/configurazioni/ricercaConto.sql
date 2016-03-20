select
	t1.*,
	coalesce(totreg1.totale, 0) as tot_registrazioni_sottoconto,
	coalesce(totreg2.totale, 0) as tot_registrazioni_conto
  from (
		select 
			'C' as tipo,
			cast(cod_conto as integer) as cod_conto,
			des_conto,
			cat_conto,
			tip_conto,
			null as cod_sottoconto,
			null as des_sottoconto
		from contabilita.conto
		where 1 = 1
		%categoria%
		%tipconto%
		union
		select
			'S' as tipo,
			cast(sottoconto.cod_conto as integer) as cod_conto,
			null as des_conto,
			null as cat_conto,
			null as tip_conto,
			cast(cod_sottoconto as integer) as cod_sottoconto,
			des_sottoconto
		from contabilita.sottoconto as sottoconto
			inner join
				(select cod_conto
				   from contabilita.conto
				  where 1 = 1
				  %categoria%
				  %tipconto%
				  ) as t1
				on t1.cod_conto = sottoconto.cod_conto  
		) as t1		
	left outer join
		(select
			cast(cod_conto as integer) as cod_conto,
			cast(cod_sottoconto as integer) as cod_sottoconto,
			count(*) as totale
		   from contabilita.dettaglio_registrazione
		    group by cod_conto, cod_sottoconto
		) as totreg1
	  on totreg1.cod_conto = t1.cod_conto
	  and totreg1.cod_sottoconto = t1.cod_sottoconto
	left outer join
		(select
			cast(cod_conto as integer) as cod_conto,
			count(*) as totale
		   from contabilita.dettaglio_registrazione
		    group by cod_conto
		) as totreg2
	  on totreg2.cod_conto = t1.cod_conto	  
order by t1.cod_conto, t1.tipo, t1.cod_sottoconto
select 
	cast(conto.cod_conto as integer) as cod_conto,
	conto.des_conto,
	conto.cat_conto,
	conto.tip_conto,
	coalesce(totreg.totale, 0) as tot_registrazioni_conto	
from contabilita.conto as conto
	left outer join
		(select
			cast(cod_conto as integer) as cod_conto,
			count(*) as totale
		   from contabilita.dettaglio_registrazione
		    group by cod_conto
		) as totreg
	  on totreg.cod_conto = cast(conto.cod_conto as integer)
where 1 = 1
%categoria%
%tipconto%
order by conto.cod_conto, conto.tip_conto

select 
	'C' as tipo,
	cod_conto,
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
	sottoconto.cod_conto,
	null as des_conto,
	null as cat_conto,
	null as tip_conto,
	cod_sottoconto,
	des_sottoconto
from contabilita.sottoconto
	inner join
		(select cod_conto
		   from contabilita.conto
		  where 1 = 1
		  %categoria%
		  %tipconto%
		  ) as t1
		on t1.cod_conto = sottoconto.cod_conto  
order by cod_conto, tipo, cod_sottoconto
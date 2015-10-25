select 
	conto.cod_conto,
	sottoconto.cod_sottoconto,
	conto.tip_conto
from contabilita.conto
	inner join contabilita.sottoconto
		on sottoconto.cod_conto = conto.cod_conto
order by conto.cod_conto, sottoconto.cod_sottoconto
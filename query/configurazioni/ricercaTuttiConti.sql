select 
	sottoconto.cod_conto,
	sottoconto.cod_sottoconto,
        sottoconto.des_sottoconto
from contabilita.sottoconto
order by sottoconto.cod_conto, sottoconto.cod_sottoconto
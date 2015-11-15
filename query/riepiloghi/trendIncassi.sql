select 
	cast(num_mese as smallint),
	des_mese,
	coalesce(registrazione.qtapag,0) as qtapag
from contabilita.mese
	left outer join
		(
		select cast(extract(month from dat_registrazione) as char) as mese, count(*) as qtapag
			from contabilita.registrazione
			where dat_registrazione between '%datareg_da%' and '%datareg_a%'
			and cod_causale in ('2035','2100')
			and cod_negozio = '%codnegozio%'
			group by mese				
		) as registrazione
		on registrazione.mese = mese.num_mese
order by num_mese
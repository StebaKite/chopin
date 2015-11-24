select 
	cast(num_mese as smallint),
	des_mese,
	coalesce(registrazione.qtapag,0) as qtapag
from contabilita.mese
	left outer join
		(
		select extract(month from dat_registrazione) as mese, count(*) as qtapag
			from contabilita.registrazione
			where dat_registrazione between '%datareg_da%' and '%datareg_a%'
			and cod_causale in ('1100','1250','1650','1800')
			and cod_negozio = '%codnegozio%'
			group by mese				
		) as registrazione
		on registrazione.mese = cast(mese.num_mese as smallint)
order by num_mese
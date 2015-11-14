SELECT DISTINCT
	extract(month from dat_registrazione) as mese
FROM contabilita.registrazione
WHERE dat_registrazione between '%datareg_da%' and '%datareg_a%'
and cod_causale in ('1100','1250','1650','1800')
order by mese
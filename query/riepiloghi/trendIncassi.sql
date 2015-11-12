SELECT 
	extract(month from dat_registrazione) as mese, count(*) as qtainc
FROM contabilita.registrazione
WHERE dat_registrazione between '01/07/2015' and '31/12/2015'
and cod_causale in ('2035','2100')
and cod_negozio = '%codnegozio%'
group by mese, cod_causale
order by mese
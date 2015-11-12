SELECT 
	extract(month from dat_registrazione) as mese, count(*) as qtapag
FROM contabilita.registrazione
WHERE dat_registrazione between '01/07/2015' and '31/12/2015'
and cod_causale in ('1100','1250','1650','1800')
and cod_negozio = '%codnegozio%'
group by mese, cod_causale
order by mese
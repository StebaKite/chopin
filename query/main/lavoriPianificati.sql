SELECT
	pk_lavoro_pianificato,
	dat_lavoro,
	des_lavoro,
	fil_esecuzione_lavoro,
	cla_esecuzione_lavoro,
	sta_lavoro
FROM contabilita.lavoro_pianificato
WHERE dat_lavoro BETWEEN '%datalavoro_da%' AND '%datalavoro_a%'
order by dat_lavoro
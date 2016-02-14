SELECT
	pk_lavoro_pianificato,
	dat_lavoro,
	des_lavoro,
	fil_esecuzione_lavoro,
	cla_esecuzione_lavoro,
	sta_lavoro,
	date(tms_esecuzione) as tms_esecuzione
FROM contabilita.lavoro_pianificato
order by dat_lavoro desc
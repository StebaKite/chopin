UPDATE contabilita.lavoro_pianificato
SET sta_lavoro = '%sta_lavoro%',
	tms_esecuzione = now()
WHERE pk_lavoro_pianificato = %pk_lavoro_pianificato%

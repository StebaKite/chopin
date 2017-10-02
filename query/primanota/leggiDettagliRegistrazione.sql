SELECT 
	detreg.id_dettaglio_registrazione,
	detreg.id_registrazione,
	detreg.imp_registrazione,
	detreg.ind_dareavere,
	detreg.cod_conto || '.' || detreg.cod_sottoconto || ' - ' || sottoconto.des_sottoconto as cod_conto,
	detreg.cod_sottoconto,
	sottoconto.des_sottoconto,
	detreg.dat_inserimento
FROM contabilita.dettaglio_registrazione as detreg
	INNER JOIN contabilita.sottoconto as sottoconto
		ON sottoconto.cod_conto = detreg.cod_conto
		AND sottoconto.cod_sottoconto = detreg.cod_sottoconto
WHERE detreg.id_registrazione = %id_registrazione%
SELECT
	sottoconto.cod_conto,
	sottoconto.cod_sottoconto,
	sottoconto.des_sottoconto,
	sottoconto.dat_creazione_sottoconto,
	coalesce(sottoconto.ind_gruppo,'NS') as ind_gruppo,
	coalesce(t1.totale, 0) as totale_registrazioni_sottoconto
FROM contabilita.sottoconto as sottoconto
	LEFT OUTER JOIN
		(SELECT
			cod_conto,
			cod_sottoconto,
			count(*) as totale
		  FROM contabilita.dettaglio_registrazione
		  GROUP BY cod_conto, cod_sottoconto
		) as t1
	 ON t1.cod_conto = sottoconto.cod_conto
	 AND t1.cod_sottoconto = sottoconto.cod_sottoconto
WHERE sottoconto.cod_conto = '%cod_conto%'
ORDER BY sottoconto.cod_sottoconto
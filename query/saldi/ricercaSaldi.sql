SELECT
	saldo.cod_negozio,
	saldo.cod_conto,
	saldo.cod_sottoconto,
	saldo.dat_saldo,
	saldo.des_saldo,
	saldo.imp_saldo,
	saldo.ind_dareavere,
	conto.des_conto,
	sottoconto.des_sottoconto
	
FROM contabilita.saldo

	INNER JOIN contabilita.conto
		ON conto.cod_conto = saldo.cod_conto

	INNER JOIN contabilita.sottoconto
		ON sottoconto.cod_conto = saldo.cod_conto
		AND sottoconto.cod_sottoconto = saldo.cod_sottoconto

WHERE cod_negozio = '%cod_negozio%'
AND   dat_saldo = '%dat_saldo%'
order by cod_conto, cod_sottoconto

SELECT
	sottoconto.cod_conto,
	sottoconto.cod_sottoconto,
	sottoconto.des_sottoconto,
	conto.des_conto
FROM contabilita.sottoconto
	INNER JOIN contabilita.conto
		ON conto.cod_conto = sottoconto.cod_conto
order by sottoconto.cod_conto, sottoconto.cod_sottoconto

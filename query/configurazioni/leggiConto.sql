SELECT
	cod_conto,
	des_conto,
	cat_conto,
	tip_conto,
	dat_creazione_conto
FROM contabilita.conto
WHERE cod_conto = '%cod_conto%'

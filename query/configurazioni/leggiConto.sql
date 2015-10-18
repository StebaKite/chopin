SELECT
	cod_conto,
	des_conto,
	cat_conto,
	tip_conto,
	dat_creazione_conto,
	ind_presenza_in_bilancio,
	num_riga_bilancio,
	ind_visibilita_sottoconti
FROM contabilita.conto
WHERE cod_conto = '%cod_conto%'

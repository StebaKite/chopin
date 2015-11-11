UPDATE contabilita.conto
SET des_conto='%des_conto%',
	cat_conto='%cat_conto%',
	tip_conto='%tip_conto%',
	ind_presenza_in_bilancio='%ind_presenza_in_bilancio%',
	num_riga_bilancio=%num_riga_bilancio%,
	ind_visibilita_sottoconti='%ind_visibilita_sottoconti%'
WHERE cod_conto='%cod_conto%'

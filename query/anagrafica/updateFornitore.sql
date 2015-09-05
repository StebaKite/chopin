UPDATE contabilita.fornitore
SET cod_fornitore='%cod_fornitore%',
	des_fornitore='%des_fornitore%',
	des_indirizzo_fornitore=%des_indirizzo_fornitore%,
	des_citta_fornitore=%des_citta_fornitore%,
	cap_fornitore=%cap_fornitore%,
	tip_addebito='%tip_addebito%',
	num_gg_scadenza_fattura = %num_gg_scadenza_fattura%
WHERE id_fornitore=%id_fornitore%

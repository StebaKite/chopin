SELECT
	id_fornitore,
	cod_fornitore,
	des_fornitore,
	des_indirizzo_fornitore,
	des_citta_fornitore,
	cap_fornitore,
	tip_addebito,
	dat_creazione,
	num_gg_scadenza_fattura
FROM contabilita.fornitore
WHERE id_fornitore = %id_fornitore%

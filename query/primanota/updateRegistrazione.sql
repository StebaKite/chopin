UPDATE contabilita.registrazione
SET dat_scadenza=%dat_scadenza%,
	des_registrazione='%des_registrazione%',
	id_fornitore=%id_fornitore%,
	id_cliente=%id_cliente%,
	cod_causale='%cod_causale%',
	num_fattura=%num_fattura%,
	dat_registrazione=%dat_registrazione%,
	sta_registrazione='%sta_registrazione%'
WHERE id_registrazione=%id_registrazione%
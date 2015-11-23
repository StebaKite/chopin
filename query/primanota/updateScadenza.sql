UPDATE contabilita.scadenza
SET 
	id_registrazione=%id_registrazione%, 
	dat_scadenza=%dat_scadenza%, 
	imp_in_scadenza=%imp_in_scadenza%, 
	nota_scadenza='%nota_in_scadenza%', 
	tip_addebito='%tip_addebito%', 
	cod_negozio=%cod_negozio%, 
	id_fornitore=%id_fornitore%, 
	num_fattura=%num_fattura%, 
	sta_scadenza='%sta_scadenza%'
	
WHERE id_scadenza=%id_scadenza%

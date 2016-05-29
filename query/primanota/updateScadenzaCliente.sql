UPDATE contabilita.scadenza_cliente
SET 
	id_registrazione=%id_registrazione%, 
	dat_registrazione=%dat_registrazione%, 
	imp_registrazione=%imp_registrazione%, 
	nota='%nota_in_scadenza%', 
	tip_addebito='%tip_addebito%', 
	cod_negozio=%cod_negozio%, 
	id_cliente=%id_cliente%, 
	num_fattura=%num_fattura%, 
	sta_scadenza='%sta_scadenza%'
	
WHERE id_scadenza=%id_scadenza%

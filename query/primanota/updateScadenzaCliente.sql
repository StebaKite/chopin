UPDATE contabilita.scadenza_cliente
SET 
	id_registrazione=%id_registrazione%, 
	dat_scadenza=%dat_scadenza%, 
	imp_in_scadenza=%imp_in_scadenza%, 
	nota='%nota_in_scadenza%', 
	tip_addebito='%tip_addebito%', 
	cod_negozio=%cod_negozio%, 
	id_cliente=%id_cliente%, 
	num_fattura=%num_fattura%, 
	sta_scadenza='%sta_scadenza%'
	
WHERE id_scadenza=%id_scadenza%

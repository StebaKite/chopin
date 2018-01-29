select
	id_scadenza, 
	id_registrazione, 
	to_char(dat_registrazione, 'DD-MM-YYYY') as dat_registrazione, 
	imp_registrazione, 
	nota, 
	tip_addebito, 
	cod_negozio, 
	id_cliente, 
	num_fattura, 
	sta_scadenza, 
	id_incasso
		
from contabilita.scadenza_cliente
where id_incasso = %id_registrazione%
order by dat_registrazione desc
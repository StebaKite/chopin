select 
	id_registrazione,	
	num_fattura 
from contabilita.scadenza_cliente
where id_incasso = %id_incasso%
and   id_cliente = %id_cliente%

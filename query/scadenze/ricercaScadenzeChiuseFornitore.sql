select
	id_scadenza, 
	id_registrazione, 
	to_char(dat_scadenza, 'DD-MM-YYYY') as dat_scadenza, 
	imp_in_scadenza, 
	nota_scadenza, 
	tip_addebito, 
	cod_negozio, 
	id_fornitore, 
	num_fattura, 
	sta_scadenza, 
	id_pagamento
		
from contabilita.scadenza
where id_pagamento = %id_registrazione%
order by dat_scadenza desc
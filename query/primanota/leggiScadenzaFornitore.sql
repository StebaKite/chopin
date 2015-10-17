select 
	id_registrazione,	
	num_fattura 
from contabilita.scadenza
where id_pagamento = %id_pagamento%
and   id_fornitore = %id_fornitore%

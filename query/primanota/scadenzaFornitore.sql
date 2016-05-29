select
	id_scadenza,
	id_pagamento,
	sta_scadenza
from contabilita.scadenza
where id_registrazione = %id_registrazione%
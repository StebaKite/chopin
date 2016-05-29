select
	id_scadenza,
	id_incasso,
	sta_scadenza
from contabilita.scadenza_cliente
where id_registrazione = %id_registrazione%
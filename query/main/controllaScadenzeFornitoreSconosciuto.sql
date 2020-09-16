select 
	scadenza.cod_negozio,
	scadenza.dat_scadenza,
	scadenza.nota_scadenza,
	fornitore.des_fornitore
from contabilita.scadenza as scadenza
	left outer join contabilita.fornitore as fornitore
	 on fornitore.id_fornitore = scadenza.id_fornitore
where fornitore.des_fornitore is null
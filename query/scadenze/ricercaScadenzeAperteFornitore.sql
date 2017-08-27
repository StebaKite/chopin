select
	num_fattura,
	imp_in_scadenza,
	nota_scadenza,
	to_char(dat_scadenza, 'DD/MM/YYYY') as dat_scadenza
		
from contabilita.scadenza
where id_fornitore = %id_fornitore%
and sta_scadenza = '00'
order by 1
select num_fattura 
from contabilita.scadenza
where id_fornitore = %id_fornitore%
and sta_scadenza = '00'
order by 1
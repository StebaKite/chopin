select num_fattura 
from contabilita.scadenza_cliente
where id_cliente = %id_cliente%
and sta_scadenza = '00'
order by 1
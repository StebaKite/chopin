select t1.*
  from (
		select num_fattura 
		from contabilita.scadenza_cliente
		where id_incasso = %id_incasso%
		
		union
		
		select num_fattura 
		from contabilita.scadenza_cliente
		where id_cliente = %id_cliente%
		and sta_scadenza = '00'
	) as t1
order by 1
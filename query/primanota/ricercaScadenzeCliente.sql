select t1.*
  from (
		select num_fattura, imp_registrazione, sta_scadenza, nota
		from contabilita.scadenza_cliente
		where id_registrazione = %id_registrazione%
		
		union
		
		select num_fattura, imp_registrazione, sta_scadenza, nota 
		from contabilita.scadenza_cliente
		where id_cliente = %id_cliente%
		and sta_scadenza = '00'
	) as t1
order by 1
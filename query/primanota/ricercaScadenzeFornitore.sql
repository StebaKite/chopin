select t1.*
  from (
		select num_fattura, imp_in_scadenza, nota_scadenza, sta_scadenza
		from contabilita.scadenza
		where id_registrazione = %id_registrazione%
		
		union
		
		select num_fattura, imp_in_scadenza, nota_scadenza, sta_scadenza
		from contabilita.scadenza
		where id_fornitore = %id_fornitore%
	) as t1
order by 1
select 
	registrazione.id_registrazione, 
	registrazione.des_registrazione
	
from contabilita.registrazione

	left outer join contabilita.dettaglio_registrazione
	on dettaglio_registrazione.id_registrazione = registrazione.id_registrazione
	
where registrazione.cod_causale = '2100'
and registrazione.dat_registrazione = '%dat_registrazione%'
and registrazione.cod_negozio = '%cod_negozio%'
and dettaglio_registrazione.cod_conto = '%cod_conto%'
and dettaglio_registrazione.imp_registrazione = %imp_registrazione%


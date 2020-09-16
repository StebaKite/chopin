select
	registrazione.des_registrazione, 
	to_char(registrazione.dat_registrazione, 'DD/MM/YYYY') as dat_registrazione,	
	registrazione.cod_negozio
from contabilita.registrazione
where id_fornitore is null
and id_cliente is null
and num_fattura is not null
select 
	registrazione.des_registrazione, 
	to_char(registrazione.dat_registrazione, 'DD/MM/YYYY') as dat_registrazione
from contabilita.registrazione as registrazione
where registrazione.cod_negozio is null

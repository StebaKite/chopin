select 
	registrazione.des_registrazione, 
	to_char(registrazione.dat_registrazione, 'DD/MM/YYYY') as dat_registrazione,	
	registrazione.cod_negozio
from contabilita.registrazione as registrazione
where registrazione.sta_registrazione = '02'
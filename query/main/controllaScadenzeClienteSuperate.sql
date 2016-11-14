select 
	scadenza.nota, 
	to_char(scadenza.dat_registrazione, 'DD/MM/YYYY') as dat_registrazione
 from contabilita.scadenza_cliente as scadenza
 where scadenza.dat_registrazione < current_date
 and scadenza.sta_scadenza = '00'
 order by 2
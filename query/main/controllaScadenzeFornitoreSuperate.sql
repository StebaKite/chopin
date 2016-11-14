select 
	nota_scadenza, 
	to_char(scadenza.dat_scadenza, 'DD/MM/YYYY') as dat_scadenza
 from contabilita.scadenza as scadenza
 where scadenza.dat_scadenza < current_date
 and scadenza.sta_scadenza = '00'
 order by 2
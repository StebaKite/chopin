select
	to_char(reg.dat_registrazione, 'DD/MM/YYYY') as dat_registrazione,
	reg.des_registrazione,
	reg.sta_registrazione,
	detreg.id_dettaglio_registrazione,
	detreg.imp_registrazione,
	detreg.ind_dareavere,
	detreg.cod_conto,
	detreg.cod_sottoconto
	
 from contabilita.registrazione as reg
 
  		inner join contabilita.dettaglio_registrazione as detreg
  			on detreg.id_registrazione = reg.id_registrazione
  			
where dat_registrazione between '%datareg_da%' and '%datareg_a%'
  %filtri-registrazione%
  %filtri-dettaglio%

order by dat_registrazione
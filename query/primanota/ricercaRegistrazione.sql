select t1.*
  from (
	select
		'R' as tipo,
		reg.id_registrazione,
		reg.dat_registrazione as dat_registrazione_yyyymmdd,
		to_char(reg.dat_registrazione, 'DD/MM/YYYY') as dat_registrazione,
		reg.des_registrazione,
		reg.cod_causale,
		reg.num_fattura,
		reg.sta_registrazione,
		causale.des_causale,
		null as id_dettaglio_registrazione,
		null as imp_registrazione,
		null as ind_dareavere,
		null as cod_conto,
		null as cod_sottoconto,
		null as des_sottoconto
	  from contabilita.registrazione as reg
	  		inner join contabilita.causale as causale
	  			on causale.cod_causale = reg.cod_causale  			  
	  where dat_registrazione between '%datareg_da%' and '%datareg_a%'
	  %filtri-registrazione%
	union
	select
		'D' as tipo,
		detreg.id_registrazione,
		reg.dat_registrazione as dat_registrazione_yyyymmdd,
		to_char(reg.dat_registrazione, 'DD/MM/YYYY') as dat_registrazione,
		null as des_registrazione,
		null as cod_causale,
		null as num_fattura,
		null as sta_registrazione,
		null as des_causale,
		detreg.id_dettaglio_registrazione,
		detreg.imp_registrazione,
		detreg.ind_dareavere,
		detreg.cod_conto,
		detreg.cod_sottoconto,
		sottoconto.des_sottoconto
	  from contabilita.registrazione as reg
	  		inner join contabilita.dettaglio_registrazione as detreg
	  			on detreg.id_registrazione = reg.id_registrazione  		
	  		inner join contabilita.sottoconto as sottoconto
	  			on sottoconto.cod_conto = detreg.cod_conto 
	  			and sottoconto.cod_sottoconto = detreg.cod_sottoconto
	  where dat_registrazione between '%datareg_da%' and '%datareg_a%'
	  %filtri-registrazione%
	  %filtri-dettaglio%
	) as t1
select t1.*
  from (
	select
		'R' as tipo,
		reg.id_registrazione,
		reg.dat_registrazione as dat_registrazione_yyyymmdd,
		to_char(reg.dat_registrazione, 'DD-MM-YYYY') as dat_registrazione,
		reg.des_registrazione,
		reg.cod_causale,
		reg.num_fattura,
		reg.sta_registrazione,
		causale.des_causale,
		scad.numpag as id_pagamento,
		scadcli.numinc as id_incasso
	  from contabilita.registrazione as reg
	  		inner join contabilita.causale as causale
	  			on causale.cod_causale = reg.cod_causale  			  
	  		left outer join (
					select id_registrazione, count(*) as numpag 
					from contabilita.scadenza
					where id_pagamento is not null
					group by id_registrazione
	  			) as scad 
		  		on scad.id_registrazione = reg.id_registrazione	  			  			  	  			
	  		left outer join (
					select id_registrazione, count(*) as numinc 
					from contabilita.scadenza_cliente
					where id_incasso is not null
					group by id_registrazione
		  		) as scadcli 
				on scadcli.id_registrazione = reg.id_registrazione	  			  			  	  			
	  where dat_registrazione between '%datareg_da%' and '%datareg_a%'
	  %filtri-registrazione%	  
	) as t1	
	order by 
		t1.dat_registrazione_yyyymmdd desc, 
		t1.id_registrazione asc
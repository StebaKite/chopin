select t1.*
  from (
            select 
                    to_char(registrazione.dat_registrazione, 'DD/MM/YYYY') as dat_registrazione,
                    registrazione.des_registrazione,
                    count(*) as numdet
            from contabilita.registrazione as registrazione
                left outer join contabilita.dettaglio_registrazione as dettaglio
                    on dettaglio.id_registrazione = registrazione.id_registrazione
            group by dat_registrazione, des_registrazione
	) as t1
where t1.numdet = 0
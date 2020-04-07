select
    assistito.des_assistito,
    t1.mese,
    t1.qta_presenze
from contabilita.assistito as assistito
    inner join
        (
            select 
                    id_Assistito,
                    cod_negozio, 
                    extract(month from dat_presenza) as mese,
                    extract(year from dat_presenza) as anno,
                    count(*) as qta_presenze
            from contabilita.presenza_assistito
            group by 1, 2, 3, 4
        ) as t1
        on t1.id_assistito = assistito.id_assistito	
where t1.cod_negozio in (%codnegozio%)
and t1.anno = '%anno%'
order by assistito.des_assistito, t1.mese
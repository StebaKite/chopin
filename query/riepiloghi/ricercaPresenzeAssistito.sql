select
    t1.cod_negozio,
    t1.id_assistito,
    assistito.des_assistito,
    t1.mese,
    t1.anno,
    t1.qta_presenze
from contabilita.assistito as assistito
    inner join
        (
            select 
                    id_Assistito,
                    cod_negozio, 
                    to_char(dat_presenza, 'MM') as mese,
                    to_char(dat_presenza, 'yyyy') as anno,				
                    count(*) as qta_presenze
            from contabilita.presenza_assistito
            group by 1, 2, 3, 4
        ) as t1
        on t1.id_assistito = assistito.id_assistito	
where t1.cod_negozio in (%codnegozio%)
and t1.mese in (%mese%)
and t1.anno = '%anno%'
order by t1.mese, assistito.des_assistito

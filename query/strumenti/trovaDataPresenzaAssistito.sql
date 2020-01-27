select id_presenza
from contabilita.presenza_assistito
where id_assistito = %id_assistito%
and   dat_presenza = '%dat_presenza%'
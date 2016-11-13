select cod_conto
from contabilita.sottoconto
where cod_sottoconto = '%cod_fornitore%'
and cod_conto in ('215','220')
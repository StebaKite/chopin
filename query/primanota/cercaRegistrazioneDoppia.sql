select
    reg.id_registrazione,
    reg.dat_registrazione as dat_registrazione_yyyymmdd,
    to_char(reg.dat_registrazione, 'DD-MM-YYYY') as dat_registrazione,
    reg.des_registrazione,
    reg.cod_causale,
    reg.num_fattura,
    reg.sta_registrazione
from contabilita.registrazione as reg
where reg.dat_registrazione = '%dat_registrazione%'
and   reg.cod_causale = '%cau_registrazione%'
and   reg.des_registrazione = '%des_registrazione%'

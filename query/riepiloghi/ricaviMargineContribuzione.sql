select sum(imp_saldo) as totalericavovendita
  from contabilita.saldo
 where cod_negozio IN (%codnegozio%)
   and cod_conto in ('400')
   and dat_saldo = '%datareg_da%'

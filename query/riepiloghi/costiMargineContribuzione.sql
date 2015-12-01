select sum(imp_saldo) as totaleCostoVariabile
  from contabilita.saldo
 where cod_negozio IN (%codnegozio%)
   and cod_conto = '300'
   and cod_sottoconto in ('10','20','40')
   and dat_saldo = '%datareg_da%'
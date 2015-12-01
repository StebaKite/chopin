select sum(imp_saldo) as totalecostofisso
  from contabilita.saldo
 where cod_negozio IN (%codnegozio%)
   and cod_conto not in ('350','360')
   and dat_saldo = '%datareg_da%'
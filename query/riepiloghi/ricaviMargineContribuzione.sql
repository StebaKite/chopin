select sum(imp_saldo) as totaleRicavoVendita
  from contabilita.saldo
 where cod_negozio IN (%codnegozio%)
   and cod_conto = '400'
   and dat_saldo = '%datareg_da%'

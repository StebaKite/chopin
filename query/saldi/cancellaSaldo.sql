DELETE FROM contabilita.saldo
WHERE cod_negozio = '%cod_negozio%'
  AND cod_conto = '%cod_conto%'
  AND cod_sottoconto = '%cod_sottoconto%'
  AND dat_saldo = '%dat_saldo%'
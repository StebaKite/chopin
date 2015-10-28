UPDATE contabilita.saldo
SET
	des_saldo='%des_saldo%',
	imp_saldo=%imp_saldo%, 
	ind_dareavere='%ind_dareavere%'
	
where cod_negozio = '%cod_negozio%'
  and cod_conto = '%cod_conto%'
  and cod_sottoconto = '%cod_sottoconto%'
  and dat_saldo = '%dat_saldo%'
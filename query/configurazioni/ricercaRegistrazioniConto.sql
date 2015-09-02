SELECT
	registrazione.dat_registrazione,
	registrazione.des_registrazione,
	dettaglio.imp_registrazione,
	dettaglio.ind_dareavere
FROM contabilita.dettaglio_registrazione as dettaglio
	INNER JOIN contabilita.registrazione as registrazione
	  ON dettaglio.id_registrazione = registrazione.id_registrazione
WHERE dettaglio.cod_conto = '%cod_conto%'
  AND dettaglio.cod_sottoconto = '%cod_sottoconto%'
%filtro_date%
ORDER BY registrazione.dat_registrazione


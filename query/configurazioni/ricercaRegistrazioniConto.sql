SELECT
	to_char(registrazione.dat_registrazione, 'DD/MM/YYYY') as dat_registrazione,
	registrazione.des_registrazione,
	dettaglio.imp_registrazione,
	dettaglio.ind_dareavere,
	conto.des_conto,
	conto.cat_conto,
	conto.tip_conto,
	sottoconto.des_sottoconto
FROM contabilita.dettaglio_registrazione as dettaglio
	INNER JOIN contabilita.registrazione as registrazione
	  ON dettaglio.id_registrazione = registrazione.id_registrazione
	INNER JOIN contabilita.conto as conto
	  ON conto.cod_conto = dettaglio.cod_conto
	INNER JOIN contabilita.sottoconto as sottoconto
	  ON sottoconto.cod_conto = dettaglio.cod_conto	  
	  AND sottoconto.cod_sottoconto = dettaglio.cod_sottoconto	  
WHERE dettaglio.cod_conto = '%cod_conto%'
  AND dettaglio.cod_sottoconto = '%cod_sottoconto%'
%filtro_date%
ORDER BY registrazione.dat_registrazione


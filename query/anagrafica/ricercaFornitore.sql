SELECT
	fornitore.id_fornitore,
	fornitore.cod_fornitore,
	fornitore.des_fornitore,
	fornitore.des_indirizzo_fornitore,
	fornitore.des_citta_fornitore,
	fornitore.cap_fornitore,
	fornitore.tip_addebito,
	fornitore.dat_creazione,
	fornitore.num_gg_scadenza_fattura,
	coalesce(t1.totale, 0) as tot_registrazioni_fornitore
FROM contabilita.fornitore as fornitore
	LEFT OUTER JOIN
		(SELECT id_fornitore, count(*) as totale
		   FROM contabilita.registrazione
		   GROUP BY id_fornitore
		) AS t1
		on t1.id_fornitore = fornitore.id_fornitore
WHERE 1 = 1
%filtri_fornitore%
ORDER BY des_fornitore

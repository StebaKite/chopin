SELECT
	mercato.id_mercato,
	mercato.cod_mercato,
	mercato.des_mercato,
	mercato.citta_mercato,
	coalesce(t1.totale, 0) as tot_registrazioni_mercato	
FROM contabilita.mercato as mercato
	LEFT OUTER JOIN
		(SELECT id_mercato, count(*) as totale
		   FROM contabilita.registrazione
		   GROUP BY id_mercato
		) AS t1
		on t1.id_mercato = mercato.id_mercato
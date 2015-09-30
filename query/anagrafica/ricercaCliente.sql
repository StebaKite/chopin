SELECT
	cliente.id_cliente,
	cliente.cod_cliente,
	cliente.des_cliente,
	cliente.des_indirizzo_cliente,
	cliente.des_citta_cliente,
	cliente.cap_cliente,
	cliente.tip_addebito,
	cliente.dat_creazione,
	coalesce(t1.totale, 0) as tot_registrazioni_cliente	
FROM contabilita.cliente as cliente
	LEFT OUTER JOIN
		(SELECT id_cliente, count(*) as totale
		   FROM contabilita.registrazione
		   GROUP BY id_cliente
		) AS t1
		on t1.id_cliente = cliente.id_cliente
WHERE 1 = 1
%filtri_cliente%
ORDER BY cod_cliente

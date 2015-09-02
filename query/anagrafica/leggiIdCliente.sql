SELECT 
	id_cliente,
	cod_cliente,
	des_cliente,
	des_indirizzo_cliente,
	des_citta_cliente,
	cap_cliente,
	tip_addebito,
	dat_creazione
FROM contabilita.cliente
WHERE id_cliente = %id_cliente%


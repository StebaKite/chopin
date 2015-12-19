UPDATE contabilita.cliente
SET cod_cliente='%cod_cliente%',
	des_cliente='%des_cliente%',
	des_indirizzo_cliente=%des_indirizzo_cliente%,
	des_citta_cliente=%des_citta_cliente%,
	cap_cliente=%cap_cliente%,
	tip_addebito='%tip_addebito%',
	cod_piva=%cod_piva%,
	cod_fisc=%cod_fisc%,
	cat_cliente=%cat_cliente%
WHERE id_cliente=%id_cliente%

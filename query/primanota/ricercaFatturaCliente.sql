SELECT
	id_registrazione,
	dat_registrazione,
	des_registrazione
FROM contabilita.registrazione
WHERE id_cliente = %id_cliente%
AND   num_fattura = '%num_fattura%'

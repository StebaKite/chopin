SELECT
	id_registrazione,
	dat_registrazione,
	des_registrazione
FROM contabilita.registrazione
WHERE id_cliente = %id_cliente%
AND   num_fattura = '%num_fattura%'
AND   extract(year from dat_registrazione) = extract(year from current_date)
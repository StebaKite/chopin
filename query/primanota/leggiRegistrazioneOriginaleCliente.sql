SELECT
	id_registrazione
FROM contabilita.registrazione
WHERE id_cliente = %id_cliente%
AND   num_fattura = %num_fattura%
AND   cod_causale in ('2110','2120','2130')
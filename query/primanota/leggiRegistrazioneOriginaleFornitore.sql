SELECT
	id_registrazione
FROM contabilita.registrazione
WHERE id_fornitore = %id_fornitore%
AND   num_fattura = %num_fattura%
AND   cod_causale not in ('1100','2110','2120','2130')
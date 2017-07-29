UPDATE contabilita.scadenza_cliente
SET imp_registrazione = %imp_registrazione%
WHERE id_cliente = %id_cliente%
AND dat_registrazione = '%dat_registrazione%'
AND num_fattura = '%num_fattura%'
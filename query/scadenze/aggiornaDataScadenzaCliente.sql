UPDATE contabilita.scadenza_cliente
SET dat_registrazione = '%dat_scadenza_nuova%'
WHERE id_cliente = %id_cliente%
AND dat_registrazione = '%dat_scadenza%'
AND num_fattura = '%num_fattura%'
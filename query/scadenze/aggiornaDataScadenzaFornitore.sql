UPDATE contabilita.scadenza
SET dat_scadenza = %dat_scadenza_nuova%
WHERE id_fornitore = %id_fornitore%
AND dat_scadenza = '%dat_scadenza%'
AND num_fattura = '%num_fattura%'
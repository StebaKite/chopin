UPDATE contabilita.scadenza
SET imp_in_scadenza = %imp_in_scadenza%
WHERE id_fornitore = %id_fornitore%
AND dat_scadenza = '%dat_scadenza%'
AND num_fattura = '%num_fattura%'
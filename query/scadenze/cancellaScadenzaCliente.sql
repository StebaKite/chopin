DELETE FROM contabilita.scadenza_cliente
WHERE id_cliente = %id_cliente%
AND   dat_registrazione = '%dat_registrazione%'
AND   num_fattura  = '%num_fattura%'
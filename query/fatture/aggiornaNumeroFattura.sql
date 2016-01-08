UPDATE contabilita.progressivo_fattura
SET num_fattura_ultimo=%num_fattura_ultimo%
WHERE cat_cliente='%cat_cliente%' 
AND neg_progr='%neg_progr%'

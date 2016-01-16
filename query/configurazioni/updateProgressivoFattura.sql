UPDATE contabilita.progressivo_fattura
SET 
	num_fattura_ultimo=%num_fattura_ultimo%, 
	nota_testa_fattura='%nota_testa_fattura%', 
	nota_piede_fattura='%nota_piede_fattura%'

WHERE cat_cliente='%cat_cliente%' 
  AND neg_progr='%neg_progr%'

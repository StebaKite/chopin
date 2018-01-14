SELECT 
	id_mercato, 
	cod_mercato, 
	des_mercato, 
	citta_mercato, 
	cod_negozio
  FROM contabilita.mercato
  WHERE id_mercato = '%id_mercato%'
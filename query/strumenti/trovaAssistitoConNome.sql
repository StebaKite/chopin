SELECT 
	id_assistito, 
	des_assistito, 
	dat_inserimento
FROM contabilita.assistito
WHERE des_assistito like '%%des_assistito%%'
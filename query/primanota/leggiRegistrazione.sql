SELECT
	id_registrazione,
	to_char(dat_scadenza, 'DD/MM/YYYY') as dat_scadenza,	
	des_registrazione,
	id_fornitore,
	id_cliente,
	cod_causale,
	num_fattura,
	to_char(dat_registrazione, 'DD/MM/YYYY') as dat_registrazione,
	dat_inserimento,
	sta_registrazione,
	cod_negozio
FROM contabilita.registrazione
WHERE id_registrazione = %id_registrazione%
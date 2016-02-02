SELECT
	registrazione.id_registrazione,
	to_char(registrazione.dat_scadenza, 'DD/MM/YYYY') as dat_scadenza,	
	registrazione.des_registrazione,
	registrazione.id_fornitore,
	fornitore.des_fornitore,
	registrazione.id_cliente,
	cliente.des_cliente,
	registrazione.cod_causale,
	registrazione.num_fattura,
	to_char(registrazione.dat_registrazione, 'DD/MM/YYYY') as dat_registrazione,
	registrazione.dat_inserimento,
	registrazione.sta_registrazione,
	registrazione.cod_negozio
	
FROM contabilita.registrazione

	LEFT OUTER JOIN contabilita.fornitore
		ON fornitore.id_fornitore = registrazione.id_fornitore

	LEFT OUTER JOIN contabilita.cliente
		ON cliente.id_cliente = registrazione.id_cliente

WHERE registrazione.id_registrazione = %id_registrazione%

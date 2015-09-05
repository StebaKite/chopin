SELECT
	id_scadenza,
	id_registrazione,
	to_char(dat_scadenza, 'DD/MM/YYYY') as dat_scadenza,
	imp_in_scadenza,
	nota_scadenza,
	tip_addebito
FROM contabilita.scadenza
WHERE dat_scadenza between '%dat_scadenza_da%' and '%dat_scadenza_a%'
ORDER BY dat_scadenza

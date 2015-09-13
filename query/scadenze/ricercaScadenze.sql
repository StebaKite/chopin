SELECT
	id_scadenza,
	id_registrazione,
	to_char(dat_scadenza, 'DD/MM/YYYY') as dat_scadenza,
	imp_in_scadenza,
	nota_scadenza,
	tip_addebito
FROM contabilita.scadenza
WHERE 1 = 1
%filtro_date%
ORDER BY dat_scadenza

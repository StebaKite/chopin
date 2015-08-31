SELECT
	id_scadenza,
	id_registrazione,
	dat_scadenza,
	imp_in_scadenza,
	nota_scadenza
FROM contabilita.scadenza
WHERE extract(month from dat_scadenza) = extract(month from current_date)
ORDER BY dat_scadenza 
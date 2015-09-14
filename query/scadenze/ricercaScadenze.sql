SELECT
	scadenza.id_scadenza,
	scadenza.id_registrazione,
	registrazione.id_fornitore,
	to_char(scadenza.dat_scadenza, 'DD/MM/YYYY') as dat_scadenza,
	scadenza.dat_scadenza as dat_scadenza_originale,
	scadenza.imp_in_scadenza,
	scadenza.nota_scadenza,
	scadenza.tip_addebito
FROM contabilita.scadenza
	INNER JOIN contabilita.registrazione
		ON registrazione.id_registrazione = scadenza.id_registrazione
WHERE 1 = 1
%filtro_date%
ORDER BY registrazione.id_fornitore, dat_scadenza_originale

SELECT
	id_evento,
	to_char(dat_evento, 'DD/MM/YYYY') as dat_evento,
	nota_evento,
	sta_evento,
	to_char(dat_cambio_stato, 'DD/MM/YYYY') as dat_cambio_stato		
FROM contabilita.evento
%filtro_eventi%
ORDER BY dat_evento
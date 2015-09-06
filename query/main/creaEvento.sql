INSERT INTO contabilita.evento
(id_evento, dat_evento, nota_evento, sta_evento, dat_cambio_stato)
VALUES(nextval('contabilita.evento_id_evento_seq'::regclass), '%dat_evento%', '%nota_evento%', '00', null)

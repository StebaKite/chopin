INSERT INTO contabilita.lavoro_pianificato
(pk_lavoro_pianificato, dat_lavoro, des_lavoro, fil_esecuzione_lavoro, cla_esecuzione_lavoro, sta_lavoro, tms_esecuzione)
VALUES(nextval('contabilita.lavoro_pianificato_pk_lavoro_pianificato_seq'::regclass), '%dat_lavoro%', '%des_lavoro%', '%fil_esecuzione_lavoro%', '%cla_esecuzione_lavoro%', '%sta_lavoro%', null);

INSERT INTO contabilita.mercato
(id_mercato, cod_mercato, des_mercato, citta_mercato, cod_negozio)
VALUES(nextval('contabilita.mercato_id_mercato_seq'::regclass), '%cod_mercato%', '%des_mercato%', '%citta_mercato%', '%cod_negozio%');
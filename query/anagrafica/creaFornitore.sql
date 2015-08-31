INSERT INTO contabilita.fornitore
(id_fornitore, cod_fornitore, des_fornitore, des_indirizzo_fornitore, des_citta_fornitore, cap_fornitore, tip_addebito, dat_creazione)
VALUES(nextval('contabilita.fornitore_id_fornitore_seq'::regclass), '%cod_fornitore%', '%des_fornitore%', '%des_indirizzo_fornitore%', '%des_citta_fornitore%', '%cap_fornitore%', '%tip_addebito%', now());

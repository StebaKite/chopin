INSERT INTO contabilita.cliente
(id_cliente, cod_cliente, des_cliente, des_indirizzo_cliente, 
des_citta_cliente, cap_cliente, tip_addebito, dat_creazione,
cod_piva, cod_fisc, cat_cliente)
VALUES(nextval('contabilita.cliente_id_cliente_seq'::regclass), '%cod_cliente%', %des_cliente%, %des_indirizzo_cliente%, 
%des_citta_cliente%, %cap_cliente%, '%tip_addebito%', now(), %cod_piva%, %cod_fisc%, %cat_cliente%);

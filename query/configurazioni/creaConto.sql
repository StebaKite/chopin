INSERT INTO contabilita.conto
(cod_conto, des_conto, cat_conto, tip_conto, dat_creazione_conto, ind_presenza_in_bilancio, num_riga_bilancio, ind_visibilita_sottoconti)
VALUES('%cod_conto%', '%des_conto%', '%cat_conto%', '%tip_conto%', now(), '%ind_presenza_in_bilancio%', '%ind_visibilita_sottoconti%', %num_riga_bilancio%)
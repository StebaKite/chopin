UPDATE contabilita.dettaglio_registrazione
SET imp_registrazione = %imp_registrazione% ,
    ind_dareavere = '%ind_dareavere%'
WHERE id_dettaglio_registrazione = %id_dettaglio_registrazione%
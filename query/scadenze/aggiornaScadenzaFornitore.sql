UPDATE contabilita.scadenza
   SET
        dat_scadenza = '%dat_scadenza_nuova%',
        imp_in_scadenza = %imp_in_scadenza%, 
        nota_scadenza = '%nota_scadenza%', 
        tip_addebito = '%tip_addebito%', 
        cod_negozio = '%cod_negozio%', 
        id_fornitore = %id_fornitore%, 
        num_fattura = '%num_fattura%', 
        sta_scadenza = '%sta_scadenza%' 
WHERE id_fornitore = %id_fornitore_orig%
AND dat_scadenza = '%dat_scadenza%'
AND num_fattura = '%num_fattura_orig%'
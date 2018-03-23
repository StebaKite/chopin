UPDATE contabilita.scadenza_cliente
   SET
        imp_registrazione = %imp_registrazione%,
        nota = '%nota%',
        tip_addebito = '%tip_addebito%',
        cod_negozio = '%cod_negozio%',
        id_cliente = %id_cliente%,
        num_fattura = '%num_fattura%',
        sta_scadenza = '%sta_scadenza%'
WHERE id_cliente = %id_cliente_orig%
AND dat_registrazione = '%dat_registrazione%'
AND num_fattura = '%num_fattura_orig%'
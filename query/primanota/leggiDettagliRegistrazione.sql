SELECT 
	detreg.id_dettaglio_registrazione,
	detreg.id_registrazione,
	detreg.imp_registrazione,
	detreg.ind_dareavere,
	detreg.cod_conto || '.' || detreg.cod_sottoconto || ' - ' || sottoconto.des_sottoconto as cod_conto,
	detreg.cod_sottoconto,
	sottoconto.des_sottoconto,
	detreg.dat_inserimento,
	case 
            when (not cliente.id_cliente isnull) then 'Y'
            when (not fornitore.id_fornitore isnull) then 'Y'
	end as ind_conto_principale	
FROM contabilita.dettaglio_registrazione as detreg
	INNER JOIN contabilita.sottoconto as sottoconto
		ON sottoconto.cod_conto = detreg.cod_conto
		AND sottoconto.cod_sottoconto = detreg.cod_sottoconto
	left outer join contabilita.cliente as cliente
		on cliente.cod_cliente = detreg.cod_sottoconto
		and detreg.cod_conto = '120'
	left outer join contabilita.fornitore as fornitore
		on fornitore.cod_fornitore = detreg.cod_sottoconto
		and detreg.cod_conto = '215'
WHERE detreg.id_registrazione = %id_registrazione%